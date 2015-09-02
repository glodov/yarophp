<?

namespace Controller\Backend;

class Webpages extends \Controller\Backend
{
	public function index()
	{
		$this->attachAngular('webpages');
		return $this->render();
	}

	public function json($response = [])
	{
		$response = (object)$response;
		$response->list = self::jsonArray(\Model\Content\Webpage::model()->findList([], 'url asc'));
		$response->languages = self::jsonArray(\Model\Language::model()->findList([], 'position asc'));
		$Webpage = \Model\Content\Webpage::model();
		$response->itemTemplate = self::jsonArray($Webpage);
		$response->controllers = $Webpage->getEnum('controller');
		return $this->outputJSON($response);
	}

	public function save()
	{
		$model = null;
		if ($model = \Helper\Request::get('model'))
		{
			$Webpage = \Model\Content\Webpage::model();
			$Webpage->setPost($model);
			if (!$Webpage->save())
			{
				return $this->error();
			}
		}
		return $this->json(['posted' => $model]);
	}

	public function delete()
	{
		$response = ['deleted' => false];
		if ($model = \Helper\Request::get('model'))
		{
			$Webpage = \Model\Content\Webpage::model();
			$Webpage->setPost($model);
			if (!$Webpage->drop())
			{
				return $this->error();
			}
			$response['deleted'] = true;
		}
		return $this->outputJSON($response);
	}
}
