The paginator for the Eloquent Query Builder and for SMVC are used the same except for the skip and take parameters are separated for Eloquent.

A typical example of controller usage:

```php
namespace App\Controllers;

use Core\Controller;
use Core\View;
use Helpers\Url;
use Helpers\Session;
use Helpers\Csrf;
use Helpers\Paginator;

class Pet extends Controller
{
    private $pet;

    public function __construct()
    {
        parent::__construct();
        $this->pet = new \Models\PetModel();

        if (Session::get('loggin') == false) {
            Url::redirect('admin/login');
        }
    }

    public function index()
    {
        $petSearch = (isset($_REQUEST['psch']) != '' ? $_REQUEST['psch'] : "");
        Session::set('petSearch', $petSearch);

        $petRows = $this->pet->petCount($petSearch); // get number of rows
        Session::set('petRows', $petRows);

        $pages = new Paginator('5', 'p');
        $pages->setTotal($petRows);
        Session::set('petPage', $pages->getInstance());

        $data['pageLinks'] = $pages->pageLinks('?', '&psch=' . $petSearch);
        $data['title'] = 'Pet';
        $data['pets'] = $this->pet->getPets($pages->getLimit2(), $pages->getPerPage(), $petSearch); // get the data

        $this->view->renderTemplate('header', $data);
        $this->view->render('pet/index', $data);
        $this->view->renderTemplate('footer', $data);
    }
```

And typical examples of getting count and retrieving records in the model are:

```php
public function petCount($petsearch = "")
{
    $petsearch = $petSearch . "%";

    return Capsule::table('pets')->where('petName', 'like', $petsearch)->count();
}

public function getPets($offset = "", $rowsPerPage = "", $petSearch = "")
{
    $petsearch = $petSearch . "%";

    return Capsule::table('pets')
                    ->where('petName', 'like', $petsearch)
                    ->orderBy('petName', 'asc')
                    ->skip($offset)->take($rowsPerPage)->get();
}
```
