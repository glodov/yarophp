<?

namespace Controller;

use \Model\User as User;

class Backend extends Base
{
	private $webpage;

	public function isAccess($method = null)
	{
		$User = $this->getUser();
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
		$this->attachScript('/js/bootstrap.min.js');
		$this->attachScript('/plugins/bower_components/angular/angular.min.js');
		$this->attachScript('/plugins/bower_components/angular-bootstrap/ui-bootstrap.min.js');
		$this->attachScript('/plugins/bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js');

		$this->attachCSS('backend.css');
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

	public function getUser()
	{
		return \Core\Runtime::get('USER');
	}

	public function index()
	{
		return $this->getView()->render();
	}

	public function login()
	{
		if ($this->hasAccess('index'))
		{
			return $this->redirect();
		}
		if ($model = \Helper\Request::get('model'))
		{
			$model = (object)$model;
			$response = (object)['result' => false];
			if ($Token = User::login($model->login, $model->password, $model->remember))
			{
				$response->result = true;
				$response->token = $Token->id;
			}
			else
			{
				$response->error = _t('ERROR_CREDENTIALS');
			}
			return $this->outputJSON($response);
		}
		$this->attachAngular('login');
		return $this->getView()->render('login');
	}

	public function logout()
	{
		User::logout();
		return $this->redirect('login');
	}

}
