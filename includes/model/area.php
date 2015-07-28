<?

namespace Model;

class Area extends \Core\Object
{

	public $id;
	public $name;

	public function getTableName() { return 'areas'; }

	public function getPrimary() { return ['id']; }

}
