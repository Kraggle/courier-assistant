interface Array<T> {
    chunk(n: number): Array<T>;
    clean(deleteValue: any): Array<T>;
    removeDupes(): Array<T>;
    average(): Number;
}

interface String {
    /**
     * Removes all duplicated words for a string.
     *
     * @returns The string with the duplicated words removed.
     */
    filter(): string;

    /**
     * The same as match but returns a boolean.
     *
     * @param regExp The value to search for.
     * @returns True is found.
     */
    bMatch(regExp: string | RegExp): Boolean;

    /**
     * Check if a string contains another string.
     *
     * @param str       The value to search for.
     * @param matchCase Weather to make the search case-sensitive.
     * @returns True if the value was found.
     */
    contains(str: string, matchCase?: Boolean): Boolean;

    /**
     * When input a string with no whitespace but uppercase
     * first letter for each word, this adds the spaces.
     *
     * @returns A correctly spaced string.
     */
    space(): string;

    /**
     * Transforms a string to title case.
     *
     * @returns The whole string in title case.
     */
    titleCase(): string;

    /**
     * Transforms a string to first character as uppercase.
     * Does not change the rest of the string.
     *
     * @returns The string with the first character in uppercase.
     */
    firstToUpper(): string;

    /**
     * Matches two values with each other.
     *
     * @param other     Another value to match with the input string.
     * @param matchCase Wether to do a case sensitive match.
     * @returns True if the values match.
     */
    equals(other: Any, matchCase?: Boolean): Boolean;

    /**
     * Used to add a given string to a string. Will only add it
     * if it is not already there.
     *
     * @param value The value to add to the string.
     * @returns The string with the value added.
     */
    addClass(value: string): string;

    /**
     * Used to remove a given string from a string.
     *
     * @param value The value to remove from the string.
     * @returns The string with the value removed.
     */
    removeClass(value: string): string;

    /**
     * Get an input value from another string. Can be regexp or string.
     *
     * @param value The value to get from the input string.
     * @returns The value found value.
     */
    get(value: string): string;
}

interface Date {
    /**
     * Add hours to a date object.
     *
     * @param hrs The hours to add.
     * @returns The date object with the hours added.
     */
    addHours(hrs: number): Date;

    /**
     * Add minutes to a date object.
     *
     * @param mins The minutes to add.
     * @returns The date object with the minutes added.
     */
    addMinutes(mins: number): Date;
}

interface JQuery {
    perfectScrollbar(settings?: object): jQuery;
    replaceTag(element: HTMLElement, newTag: string, keepProps?: boolean): this;
    inArray(value: any, array: any[], fromIndex?: number): number;

    /**
     * Serialize a form into a javascript object.
     */
    serializeObject(): object;

    /**
     * Serialize a form into a json string.
     */
    serializeJSON(): string;

    /**
     * Used to enable an input and surrounding group.
     */
    enable();

    /**
     * Used to disable an input and surrounding group.
     */
    disable();
}

interface JQueryStatic {
    replaceTag(newTag: string, keepProps?: boolean): this;
    hasAttr(attribute: string): boolean;
}

interface Window {
    $: JQueryStatic;
    Alpine: Alpine;
    K: object;
    DateDropper(): dateDropper;
    dayjs(): dayjs;
    tippy: Tippy<Props>;
    refreshAll(): void;
    loader: Loader;
    _K: object;
    DataTable: DataTable;
    timed: timed;
    Trix: Trix;
}

interface ImportMeta {
    glob(a: Array<T>): void;
}
