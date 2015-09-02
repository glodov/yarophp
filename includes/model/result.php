<?

namespace Model\Schema\Table;

class Result extends \Core\Database\Schema\Table
{
	public function getName()
	{
		return 'results';
	}

	protected function getFields()
	{
		return [
			'id'          => '*id',
			'area_id'     => ['int'],
			'risk_id'     => ['int'],
			'probability' => ['int'],
			'impact'      => ['int'],
			'severity'    => ['int']
		];
	}

	protected function getIndexes()
	{
		return [];
	}
}

namespace Model;

class Result extends \Core\Object
{

	public $id;
	public $area_id;
	public $risk_id;
	public $probability;
	public $impact;
	public $severity;

	public function getArea()
	{
		return Area::model()->findItem(['id = '.$this->area_id]);
	}

	public function getRisk()
	{
		return Risk::model()->findItem(['id = '.$this->risk_id]);
	}

}
