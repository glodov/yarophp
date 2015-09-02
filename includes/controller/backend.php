<?

namespace Controller;

use \Model\User as User;
use \Helper\Request as Request;
use \Helper\URL as URL;

class Backend extends Base
{
	private $webpage;

	public function isAccess($method = null, \Model\User $User = null)
	{
		if (null === $User)
		{
			$User = $this->getUser();
		}
		if (!$User->id || $User->level < User::LEVEL_MODERATOR)
		{
			return in_array(strtolower($method), ['login']);
		}
		if ($User->level >= User::LEVEL_MODERATOR)
		{
			return true;
		}
		return false;
	}

	public function noAccess()
	{
		if ($this->isAjax())
		{

		}
		else
		{
			return $this->redirect('login');
		}
	}

	public function noMethod()
	{
	}

	public function getTitle()
	{
		return _t('BACKEND_TITLE');
	}

	protected function hasAccess($method)
	{
		return $this->getUser()->level >= User::LEVEL_MODERATOR;
	}

	public function getWebpage()
	{
		if (null === $this->webpage)
		{
			$this->webpage = \Model\Content\Webpage::model();
			$this->webpage->seo->title = $this->getTitle();
		}
		return $this->webpage;
	}

	protected function onLoad($method, $args)
	{
		\Helper\Console::log('Loaded: ' . $method . '(' . implode(', ', $args) . ')');
		$this->attachScript('/plugins/bower_components/jquery/dist/jquery.min.js');
		$this->attachScript('/plugins/bower_components/jquery-ui/jquery-ui.min.js');
		$this->attachScript('/plugins/bower_components/moment/moment.js');
		$this->attachScript('/js/bootstrap.min.js');
		$this->attachScript('/plugins/bower_components/angular/angular.min.js');
		$this->attachScript('/plugins/bower_components/angular-bootstrap/ui-bootstrap.min.js');
		$this->attachScript('/plugins/bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js');
		$this->attachScript('/plugins/bower_components/angular-ui-sortable/sortable.min.js');
		$this->attachScript('/plugins/bower_components/ngstorage/ngStorage.min.js');
		$this->attachScript('/plugins/bower_components/angular-bootstrap-datetimepicker/src/js/datetimepicker.js');

		$this->attachCSS('backend.css');
		$this->attachCSS('/plugins/bower_components/angular-bootstrap-datetimepicker/src/css/datetimepicker.css');
		$this->attachAngular('default');

		\Helper\Locale::load('Backend');
	}

	protected function attachAngular($app)
	{
		$files = ['app.js', 'controllers.js', 'directives.js', 'factories.js'];
		foreach ($files as $file)
		{
			$this->attachScript('/app/backend/' . $app . '/' . $file);
		}
	}

	protected function outputJSON($response, $exit = true)
	{
		if (!is_object($response))
		{
			$response = (object)$response;
		}
		if (!$this->getUser()->id)
		{
			header('HTTP/1.1 401 Unauthorized');
			$response->redirect = URL::get(new Backend) . '/login';
			$response->error = _t('ERROR_UNAUTHORIZED');
		}
		if (!$this->isAccess(Request::get('action')))
		{
			header('HTTP/1.1 403 Forbidden');
			$response->redirect = URL::get(new Backend);
			$response->error = _t('ERROR_FORBIDDEN');
		}
		return parent::outputJSON($response);
	}

	protected function jsonArray($data, $only = null)
	{
		$result = [];
		if (is_array($data))
		{
			foreach ($data as $key => $value)
			{
				$result[$key] = self::jsonArray($value, $only);
			}
		}
		else if (is_object($data) && method_exists($data, 'toArray'))
		{
			$result = $data->toArray($only);
		}
		else
		{
			$result = $data;
		}
		return $result;
	}

	public function getUser()
	{
		return \Core\Runtime::get('USER');
	}

	private function getMenu()
	{
		return [
			'fa-dashboard'   => new Backend,
			'fa-language'    => new Backend\Languages,
			'fa-file-text-o' => new Backend\Webpages,
			'fa-users'       => new Backend\Users,
		];
	}

	protected function error($error = null)
	{
		if (null === $error)
		{
			$error = _t('ERROR_DATABASE') . ': ' . \Core\Database::getInstance()->getError(true);
		}
		header('HTTP/1.1 500 Server script error');
		return $this->outputJSON(['error' => $error]);
	}

	public function index()
	{
		return $this->render();
	}

	public function login()
	{
		if ($this->hasAccess('index'))
		{
			return $this->redirect();
		}
		if ($model = Request::get('model'))
		{
			$model = (object)$model;
			$response = (object)['result' => false];
			if ($Token = User::login($model->login, $model->password, $model->remember))
			{
				$User = $Token->getObject();
				if (!$User->is_active)
				{
					header('HTTP/1.1 403 Forbidden');
					$response->error = _t('ERROR_USER_DISABLED');
				}
				else if ($this->isAccess('index', $User))
				{
					$response->result = true;
					$response->token = $Token->id;
				}
				else
				{
					header('HTTP/1.1 403 Forbidden');
					$response->error = _t('ERROR_FORBIDDEN');
				}
			}
			else
			{
				$response->error = _t('ERROR_CREDENTIALS');
			}
			return parent::outputJSON($response);
		}
		$this->attachAngular('login');
		return $this->render('login');
	}

	public function logout()
	{
		User::logout();
		return $this->redirect('login');
	}

	public function json()
	{
		$response = (object)[];
		$response->menu = [];
		foreach ($this->getMenu() as $icon => $Ctrl)
		{
			if (!$Ctrl->isAccess('index'))
			{
				continue;
			}
			$item = (object)[];
			$item->icon      = $icon;
			$item->url       = URL::get($Ctrl);
			$item->title     = _t('MENU_' . strtoupper(str_replace('Controller_', '', str_replace('\\', '_', get_class($Ctrl)))));
			$response->menu[] = $item;
		}
		$response->base_url = URL::get(new Backend);
		$response->title = $this->getTitle();
		return $this->outputJSON($response);
	}
}
