  The paginator for the Eloquent Query Builder and for SMVC are used the same
  except for the skip and take parameters are separated for Eloquent. 
  A typical example of controller usage:

~~~
    
    namespace Controllers;

    use Core\View;
    use \Helpers\Url;
    use \Helpers\Session;
    use Helpers\Csrf;
    use \Helpers\Paginator as HelpersPaginator;

    class Pet extends \Core\Controller
    {

    private $Pet;

    public function __construct()
    {
        parent::__construct();
        $this->Pet = new \Models\PetModel();
        if (Session::get('loggin') == false) {
            Url::redirect('admin/login');
        }
    }

    public function index()
    {
        $petsearch = (isset($_REQUEST['psch']) <> '' ? $_REQUEST['psch'] : "");
        Session::set('petsearch', $petsearch);
        $petrows = $this->Pet->petCount($petsearch); // get number of rows
        Session::set('petrows', $petrows);
        $pages = new HelpersPaginator('5', 'p');
        $pages->setTotal($petrows);
        Session::set('petpage', $pages->getInstance());
        $data['pageLinks'] = $pages->pageLinks('?', '&psch=' . $petsearch);
        $data['title'] = 'pet';
        $data['pets'] = $this->Pet->getPets($pages->getLimit2(), $pages->getPerpage(), $petsearch); //get the data
        $this->view->renderTemplate('header', $data);
        $this->view->render('pet/index', $data);
        $this->view->renderTemplate('footer', $data);
    }
~~~
  And typical examples of getting count and retrieving records
  in the model are:
  
~~~
    public function petCount($petsearch = "")
    {
        $petsearch = $petsearch . "%";
        return Capsule::table('pets')->where('petname', 'like', $petsearch)->count();
    }

    public function getPets($offset = "", $rowsperpage = "", $petsearch = "")
    {
        $petsearch = $petsearch . "%";
        return Capsule::table('pets')
                        ->where('petname', 'like', $petsearch)
                        ->orderBy('petname', 'asc')
                        ->skip($offset)->take($rowsperpage)->get();
    }
~~~
