<?php 
declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\ProductFacade;


class HomepagePresenter extends Nette\Application\UI\Presenter
{
	use Nette\SmartObject;


	private ProductFacade $facade;
    private Nette\Database\Explorer $database;


    
    public function __construct(
        Nette\Database\Explorer $database, 
        ProductFacade $facade)
	{
        $this->database = $database;
        $this->facade = $facade;
	}

 /**Render defaultní stránky */

    public function renderDefault(): void
	{
		$this->template->products = $this->facade->getProducts();
        $this->template->category = $this->facade->getCategories();
        $this->template->tag = $this->facade->getTags();
        $this->template->productTags = $this->facade->getProductTags();
        
	}
   

    /**Formulář na tvorbu a editaci produktu */

    public function createComponentProductForm(): Form
    {
        $form = new Form;
        
        $form->addText('name', 'Název produktu')
            ->setRequired()
            ->setHtmlAttribute('class', 'form-control');

            $form->addInteger('price', 'Cena')
                ->setRequired();

        $form->addText('datetime', 'Datum publikace')
                ->setRequired()
                ->setType('date');

        $form->addSelect('FK_category', 'Kategorie', $this->facade->getCategories()->fetchPairs('id','name'));

        $form->addCheckboxList('tag_name', 'Tagy', $this->facade->getTags()->fetchPairs('id','name'));

        $form->addRadioList('active', 'Aktivní?')
            ->setItems([
            'Aktivní' => 'Aktivní',
            'Neaktivní' => 'Neaktivní'
            ])
            ->setDefaultValue('Aktivní');

        $form->addSubmit('send', 'Uložit a publikovat');
        $form->onSuccess[] = [$this, 'productFormSucceeded'];

        return $form;
    }

    /** Potvrzení produktového formuláře */

    public function productFormSucceeded(Form $form, array $values): void
    {
        $productId = $this->getParameter('productId');

    
        $tags = $values['tag_name'];
    
        if ($productId) {
            $products = $this->facade->updateProduct((int)$productId, $values);
            $this->database->table('product_tag')->where('FK_product =?', $productId)->delete();
            foreach ($tags as $t){
                $this->database->table('product_tag')->insert([
                    'FK_product' => $productId,
                    'FK_tag' => $t
                ]);
            }
    
        } else {
            $products = $this->facade->addProduct($values, $tags);
        }

        $this->flashMessage('Produkt byl úspěšně publikován.', 'success');
        $this->redirect(':default');
    }


    /**Render editační stránky a načtení defaultních hodnot */

    public function renderEdit(int $productId): void
    {
        dump($products = $this->database
            ->table('product')
            ->get($productId));
        
        if (!$products) {
            $this->error('Produkt nenalezen');
        }

        $this->getComponent('productForm')
            ->setDefaults($products->toArray());
    }

 /**Action na smazání produktu */
    public function actionDelete($id)
    {
        if(count($this->facade->getProducts()) <= 2){
            $success = false;
            $this->flashMessage('Product nelze smazat.', 'fail');
        }
        if ($this->user->isLoggedIn() && $this->user->getIdentity()->role == 1)
        {
            $success = true;
            $this->facade->deleteProduct((int)$id);
            $this->redirect(':default');
        }
        else
            $success = false;
            $this->flashMessage('Produkt nelze smazat, či nemáte oprávnění.', 'fail');


    }


    /**Formulář na tvorbu kategorií */

    public function createComponentCategoryForm(): Form
    {
        $form = new Form;
        
		$form->addText('name', 'Název kategorie')
			->setRequired()
			->setHtmlAttribute('class', 'form-control');

		$form->addSubmit('save', 'Přidat')
			->setHtmlAttribute('class', 'btn btn-success');
		$form->onSuccess[] = [$this, 'categoryFormSucceeded'];
		return $form;
    }

 /**Potvrzení formuláře na tvorbu kategorií */
    public function categoryFormSucceeded(Form $form, $values): void
    {
		if($this->facade->getCategories()->where('name', $values->name)->fetch()){
			$form['name']->addError('Jméno je již použito.');
			$this->flashMessage('Kategorie již existuje', 'danger');
		}else{
			$this->facade->addCategory($values->name);
			$this->flashMessage('Kategorie přidána', 'success');
            $this->redirect(':default');
		}
		return;
    }

    /**Smazání kategorie */
    public function actionDeleteCategory(int $id) {
				$this->facade->deleteCategory($id);
				$this->flashMessage('Kategorie úspěšně smazána','success');
				$this->redirect(':default');
    }
    
    /**Formulář na tvorbu tagu */
    public function createComponentTagForm(): Form
    {
        $form = new Form;
        
    $form->addText('name', 'Název tagu')
        ->setRequired()
        ->setHtmlAttribute('class', 'form-control');

    $form->addSubmit('save', 'Přidat')
        ->setHtmlAttribute('class', 'btn btn-success');
    $form->onSuccess[] = [$this, 'tagFormSucceeded'];
    return $form;
}

/** Potvrzení formuláře na tagy */
public function tagFormSucceeded(Form $form, $values): void
{
    if($this->facade->getTags()->where('name', $values->name)->fetch()){
        $form['name']->addError('Jméno je již použito.');
        $this->flashMessage('Tag již existuje', 'danger');
    }else{
        $this->facade->addTag($values->name);
        $this->flashMessage('Tag přidán', 'success');
        $this->redirect(':default');
    }
    return;
}

/**Smazání tagu */
public function actionDeleteTag(int $id) {
    $this->facade->deleteTag($id);
    $this->flashMessage('Tag úspěšně smazán','success');
    $this->redirect(':default');
}



}

