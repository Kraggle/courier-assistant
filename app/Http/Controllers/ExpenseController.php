<?php

namespace App\Http\Controllers;

use App\Helpers\K;
use App\Helpers\Msg;
use App\Models\Expense;
use App\Models\RepeatRule;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Spatie\SimpleExcel\SimpleExcelReader;

class ExpenseController extends FilesController {

    /**
     * Show a list of the users expenses.
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function show() {
        // $rule = RepeatRule::get()->last();
        // $rule->createRepeats();

        return view('expense.show', ['user' => K::user()]);
    }

    /**
     * Add a new expense.
     * 
     * @param \Illuminate\Http\Request $request
     * @return redirect
     */
    public function add(Request $request) {
        $request->validate([
            'date' => ['required', 'date:Y-m-d'],
            'date_to' => ['required', 'date:Y-m-d'],
            'cost' => ['required', 'decimal:0,2'],
            'type' => ['required', 'string'],
            'describe' => ['required', 'string'],
            'image' => 'mimes:xpm,tif,jfif,gif,svg,webp,svgz,jpeg,jpg,png,bmp,pjp,apng,pjpeg,avif,pdf',
            'repeat' => ['required', 'string'],
            'every' => ['required', 'string'],
            'every_x' => ['required', 'integer', 'min:1', 'max:999'],
            'month' => ['required', 'string'],
        ]);

        $user = $request->user();

        $repeat = $request->input('repeat');
        if ($repeat == 'never') {
            $link = $request->hasFile('image') ? $this->uploadFile($request->file('image'), "images/{$user->id}/expense") : null;

            $user->expenses()->create(K::merge($request->all(), [
                'image' => $link,
                'repeat_id' => null
            ]));
        } else {
            $rules = $user->repeats()->create([
                'start_date' => $request->input('date'),
                'end_date' => $request->input('date_to'),
                'rules->repeat' => $request->input('repeat'),
                'rules->every' => $request->input('every'),
                'rules->every_x' => $request->input('every_x'),
                'rules->month' => $request->input('month'),
                'item->describe' => $request->input('describe'),
                'item->cost' => $request->input('cost'),
                'item->type' => $request->input('type')
            ]);
            $rules->createRepeats();
        }

        return back()->with('success', Msg::added('expense'));
    }

    /**
     * Edit an existing expense.
     * 
     * @param  Request  $request
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, Expense $expense) {
        $request->validate([
            'date' => ['required', 'date:Y-m-d'],
            'date_to' => ['required', 'date:Y-m-d'],
            'cost' => ['required', 'decimal:0,2'],
            'type' => ['required', 'string'],
            'describe' => ['required', 'string'],
            'image' => 'mimes:xpm,tif,jfif,gif,svg,webp,svgz,jpeg,jpg,png,bmp,pjp,apng,pjpeg,avif,pdf',
            'repeat' => ['required', 'string'],
            'every' => ['required', 'string'],
            'every_x' => ['required', 'integer', 'min:1', 'max:999'],
            'month' => ['required', 'string'],
            'choice' => 'string'
        ]);

        if ($request->hasFile('image')) {
            if ($expense->hasImage())
                $this->deleteFile($expense->image);

            $link = $this->uploadFile($request->file('image'), "images/{$request->user()->id}/expense");
            $expense->image = $link;
            $expense->save();
        }

        $expense->update($request->except(['image']));

        $choice = $request->input('choice');
        if ($expense->isRepeat() && $choice != 'this') {
            $repeat = $expense->repeat;
            $repeat->update([
                'end_date' => $request->input('date_to'),
                'rules->repeat' => $request->input('repeat'),
                'rules->every' => $request->input('every'),
                'rules->every_x' => $request->input('every_x'),
                'rules->month' => $request->input('month'),
                'item->describe' => $request->input('describe'),
                'item->cost' => $request->input('cost'),
                'item->type' => $request->input('type')
            ]);
            $repeat->createRepeats($choice == 'next' ? $expense->date : null);
        }

        return back()->with('success', Msg::edited('expense'));
    }

    /**
     * Destroy an expense and delete its image.
     * 
     * @param \Illuminate\Http\Request
     * @param \App\Models\Expense
     * @return redirect
     */
    public function destroy(Request $request, Expense $expense) {

        $choice = $request->input('choice');
        if ($expense->isRepeat() && $choice != 'this') {
            $rule = $expense->repeat;
            if ($choice == 'next') {
                $rule->deleteRepeats($expense->date);
            } else {
                $rule->delete();
            }

            return back()->with('success', Msg::deleted('recurring expenses'));
        }

        $expense->delete();

        return back()->with('success', Msg::deleted('expense'));
    }

    /**
     * Bulk add new rates.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulk(Request $request) {
        if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
            return back()->with('error', Msg::invalidFile());
        }

        $user = K::user();
        $required = ['describe', 'date', 'cost'];
        $headers = SimpleExcelReader::create($request->file('file'), 'csv')->getHeaders();
        foreach ($required as $r) {
            if (!in_array($r, $headers))
                return back()->with('error', Msg::bulkHeaders($required));
        }

        SimpleExcelReader::create($request->file('file'), 'csv')->getRows()
            ->each(function (array $row) use ($user) {
                $row = K::castArray($row, [
                    'date' => 'date:Y-m-d',
                    'describe' => 'string',
                    'cost' => ['default:0', 'float'],
                    'type' => ['default:work', 'string']
                ]);

                if ($user->hasExpense($row['date'], $row['describe'], $row['cost']))
                    return;

                $user->expenses()->create($row);
            });

        return back()->with('success', Msg::added('expenses'));
    }

    /**
     * Export all of the user's expenses.
     * 
     * @param \Illuminate\Http\Request
     * @return void
     */
    public function export(Request $request) {
        $this->doExport($request->user()->expenses, ['date', 'describe', 'cost', 'type', 'image'], 'expenses');
    }
}
