<?php
declare(strict_types=1);

namespace App\Model;

use Nette;
use Nette\Database\ResultSet;
use Nette\Database\Table\IRow;
use Nette\Database\Table\Selection;
use Nette\Utils\ArrayHash;


class ProductFacade
{
	use Nette\SmartObject;

    const
		TABLE_PRODUCT = 'product',
		COLUMN_ID = 'id',
		COLUMN_NAME = 'name',
		COLUMN_PRICE = 'price',
        COLUMN_DATETIME = 'datetime',
        COLUMN_ACTIVE = 'active',
		COLUMN_CATEGORY  = 'FK_category',

		TABLE_CATEGORY = 'category',
		CATEGORY_ID = 'id',
		CATEGORY_NAME = 'name',

        TABLE_TAG = 'tag',
        TAG_ID = 'id',
        TAG_NAME = 'name',

        TABLE_PRODUCT_TAG = 'product_tag',
        PRODUCT_TAG_PRODUCTID = 'FK_product',
        PRODUCT_TAG_TAGID = 'FK_tag'
	;


	/**
	 * @var Nette\Database\Context $database
	 */
	private $database;

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	/**
	 * Return all product at database.
	 * @param string $category Only products belonging to this cat. Don't filter when NULL.
     * @param int $visible Show all products except hidden ones.
	 * @return Selection
	 */


    /**
     * Create new product
     * @param array $values Information about product
     * @return bool True if product was created
     */
    public function addProduct(array $values, array $tags): bool
    {
        $result = $this->database->table(self::TABLE_PRODUCT)->insert([
            self::COLUMN_NAME => $values['name'],
            self::COLUMN_PRICE => $values['price'],
            self::COLUMN_DATETIME => $values['datetime'],
            self::COLUMN_CATEGORY => $values['FK_category'],
            self::COLUMN_ACTIVE => $values['active'],
        ]);
        if($result){
            try{
            foreach ($tags as $t){
                $this->database->table(self::TABLE_PRODUCT_TAG)->insert([
                    self::PRODUCT_TAG_PRODUCTID => $result->id,
                    self::PRODUCT_TAG_TAGID => $t
                ]);
            }
            }catch (Nette\Database\UniqueConstraintViolationException $e) {
				$this->database->rollBack();
				throw new DuplicateNameException;
			}
        }
        return ($result != false);
    }


    /**
     * Update existing product
     * @param int $id Catalog number of updated product
     * @param array $values Information about product
     * @return bool True if product was updated
     */
    public function updateProduct(int $id, $values): bool
    {
        $row = $this->database->table(self::TABLE_PRODUCT)->get($id);

        $result = $row->update([
            self::COLUMN_ID => $id,
            self::COLUMN_NAME => $values['name'],
            self::COLUMN_PRICE => $values['price'],
            self::COLUMN_DATETIME => $values['datetime'],
            self::COLUMN_CATEGORY => $values['FK_category'],
            self::COLUMN_ACTIVE => $values['active'],
        ]);
        return ($result != false);
    }

        /**
     * Delete product
     * @param string $product Product to be deleted
     * @return bool Returns true if product was deleted
     */
	public function deleteProduct(int $id): bool
    {
        try{
            $this->database->beginTransaction();
            $productTag = $this->database->table(self::TABLE_PRODUCT_TAG)->where(self::PRODUCT_TAG_PRODUCTID, $id)->delete();
            if($productTag){
                $product_deleted = $this->database->table(self::TABLE_PRODUCT)->where(self::COLUMN_ID, $id)->delete();
                $this->database->commit();
            }
        } catch (Nette\Database\ForeignKeyConstraintViolationException $e) {
            return false;
        }

        return ($product_deleted == 1);
    }

    	/**
	 * Return product with equal ID.
	 * @param int $id
	 * @return IRow
	 */
	public function getProduct(int $id): ?Irow {
		$product = $this->database->table(self::TABLE_PRODUCT)->get($id);
		if (!$product) {
			return null;
		}
		return $product;
	}


    public function getProducts(){
        return $this->database->table(self::TABLE_PRODUCT);
    }

    public function getProductTags(){
        return $this->database->table(self::TABLE_PRODUCT_TAG);
    }


    public function addCategory(string $name){
			$this->database
				->table(self::TABLE_CATEGORY)
				->insert([
					self::CATEGORY_NAME => $name,
				]);

    }

    public function updateCategory(int $id, $values): bool
    {
        $row = $this->database->table(self::TABLE_CATEGORY)->get($id);

        $result = $row->update([
            self::CATEGORY_ID => $values['id'],
            self::CATEGORY_NAME => $values['name'],
        ]);
        return ($result != false);
    }

    public function deleteCategory(int $id): bool
    {
        $category = $this->database->table(self::TABLE_CATEGORY);
        $neurceno = $category->where(self::CATEGORY_NAME, "Neurčeno")->fetchField(self::CATEGORY_ID);
        $toUpdate = $this->database->table(self::TABLE_PRODUCT)->where(self::COLUMN_CATEGORY, $id);
        try{ 
            $this->database->beginTransaction();
            if($neurceno == $id){
            }else{
                $updated = $toUpdate->update([self::COLUMN_CATEGORY => $neurceno]);
                if(count($toUpdate) == 0){
                    $category->where(self::CATEGORY_ID, $id)->delete();
                }
            }
                $this->database->commit();
        } catch (Nette\Database\ForeignKeyConstraintViolationException $e) {
            return false;
        }

        return true;
    }

    public function getCategories(){
        return $this->database->table(self::TABLE_CATEGORY);
    }

    public function addTag(string $name){

			$this->database
				->table(self::TABLE_TAG)
				->insert([
					self::TAG_NAME => $name,
				]);
    }

    public function updateTag(int $id, $values): bool
    {
        $row = $this->database->table(self::TABLE_TAG)->get($id);

        $result = $row->update([
            self::TAG_ID => $values['id'],
            self::TAG_NAME => $values['name'],
        ]);
        return ($result != false);
    }

    public function deleteTag(int $tag): bool
    {
        try{
            $this->database->beginTransaction();
            $product_tag = $this->database->table(self::TABLE_PRODUCT_TAG)->where(self::PRODUCT_TAG_TAGID, $tag)->delete();
            if($product_tag){
                $this->database->table(self::TABLE_TAG)->where(self::TAG_ID, $tag)->delete();
            }
            $this->database->commit();
        } catch (Nette\Database\ForeignKeyConstraintViolationException $e) {
            return false;
        }

        return true;
    }

    public function getTags(){
        return $this->database->table(self::TABLE_TAG);
    }
}
