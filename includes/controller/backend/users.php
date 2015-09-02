<?

namespace Controller\Backend;

class Users extends \Controller\Backend
{
	public function index()
	{
		$this->attachAngular('users');
		return $this->render();
	}

	public function json()
	{
		$response = (object)[];
		$response->list = self::jsonArray(\Model\User::model()->findList([], 'login asc'));
		$response->languages = self::jsonArray(\Model\Language::model()->findList([], 'position asc'));
		$User = \Model\User::model();
		$User->level = 0;
		$User->is_active = 0;
		$response->itemTemplate = self::jsonArray($User);
		return $this->outputJSON($response);
	}

	public function save()
	{
		if ($model = \Helper\Request::get('model'))
		{
			$User = \Model\User::model();
			$User->setPost($model);
			if (!$User->save())
			{
				header('HTTP/1.1 500 Database error');
				$response = (object)[];
				$response->error = _t('ERROR_DATABASE') . ': ' . \Core\Database::getInstance()->getError(true);
				return $this->outputJSON($response);
			}
		}
		return $this->json();
	}

}
