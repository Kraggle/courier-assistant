<?

namespace App\Helpers;

use App\Models\Post;

class Posts {
	/**
	 * Get the posts.
	 * 
	 * @param string|null $type
	 * @param bool $live
	 */
	public static function get(string $type = null, bool $live = true) {
		$posts = Post::all()->orderBy('date');
		if ($type)
			$posts = $posts->where('type', $type);
		if ($live)
			$posts = $posts->where('is_live', true);

		return $posts;
	}

	/**
	 * See if there are any posts by type.
	 * 
	 * @param string $type
	 * @return bool
	 */
	public static function has(string $type = null): bool {
		return self::get($type) != null;
	}
}
