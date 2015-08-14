<?

namespace Core;

/**
 * The Object I18n class.
 * Using to add multilanguage functionality to models.
 * 
 * @abstract
 * @author Yarick.
 * @version 0.2
 */
abstract class ObjectI18n extends Object
{
	
	/**
	 * Returns array of multilanguage columns.
	 * 
	 * @access public
	 * @return array The multilanguage columns.
	 */
	public function getI18nColumns();

}