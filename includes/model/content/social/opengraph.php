<?

namespace Model\Content\Social;

class OpenGraph
{
	public
		// basic
		$title,
		$type,
		$url,
		$image,

		// optional
		$audio,
		$desciprion,
		$determiner,
		$locale,
		$locale_alt = [],
		$site_name,
		$vide;

	protected $images;
}
