<?

namespace Model;

class Result extends \Core\Object
{

	public $id;
	public $area_id;
	public $risk_id;
	public $probability;
	public $impact;
	public $severity;

	public function getTableName() { return 'results'; }

	public function getPrimary() { return ['id']; }

	public function getArea()
	{
		return Area::model()->findItem(['id = '.$this->area_id]);
	}

	public function getRisk()
	{
		return Risk::model()->findItem(['id = '.$this->risk_id]);
	}

}
