<?

namespace Controller\Backend;

class Languages extends \Controller\Backend
{
	protected function onLoad($method, $args)
	{
		parent::onLoad($method, $args);
		\Helper\Locale::load('Backend/Languages');
	}

	public function index()
	{
		$this->attachAngular('languages');
		return $this->render();
	}

	public function json()
	{
		$response = (object)[];
		$response->list = self::jsonArray(\Model\Language::model()->findList([], 'position asc'));
		$Language = \Model\Language::model();
		$Language->is_active = 0;
		$response->itemTemplate = self::jsonArray($Language);
		return $this->outputJSON($response);
	}

	public function save()
	{
		if ($model = \Helper\Request::get('model'))
		{
			$Language = \Model\Language::model();
			$Language->setPost($model);
			if (!$Language->save())
			{
				return $this->error();
			}
		}
		return $this->json();
	}

	public function check()
	{
		$response = (object)[];
		if ($model = \Helper\Request::get('model'))
		{
			$Language = \Model\Language::model()->findItem(['id = ' . $model['id']]);
			if ($Language->id)
			{
				$count = \Model\I18n::model()->findSize(['locale = ' . $Language->locale]);
				if ($count)
				{
					$response->confirm = _t('CONFIRM_DELETE_DEPENDENCY', $count);
				}
				else
				{
					$response->none = true;
				}
			}
		}
		return $this->outputJSON($response);
	}

	public function pos()
	{
		if ($items = \Helper\Request::get('items'))
		{
			foreach ($items as $i => $id)
			{
				$Language = \Model\Language::model()->findItem(['id = ' . $id]);
				if ($Language->id)
				{
					$Language->position = $i + 1;
					$Language->save();
				}
			}
		}
		return $this->outputJSON([]);
	}

	public function delete()
	{
		if ($model = \Helper\Request::get('model'))
		{
			$Language = \Model\Language::model()->findItem(['id = ' . $model['id']]);
			if ($Language->id)
			{
				if (!$Language->drop())
				{
					return $this->error();
				}
			}
		}
		return $this->json();
	}
}
