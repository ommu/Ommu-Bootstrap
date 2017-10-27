<?php
/**
 * This is the template for generating the model class of a specified table.
 * - $this: the ModelCode object
 * - $tableName: the table name for this class (prefix is already removed if necessary)
 * - $modelClass: the model class name
 * - $columns: list of table columns (name=>CDbColumnSchema)
 * - $labels: list of attribute labels (name=>label)
 * - $rules: list of validation rules
 * - $relations: list of relations (name=>relation declaration)
 */
 
/* 
* set name relation with underscore
*/
function setRelationName($names) {
	$char=range("A","Z");
	foreach($char as $val) {
		if(strpos($names, $val) !== false) {
			$names = str_replace($val, '_'.strtolower($val), $names);
		}
	}
	return $names;
}

?>
<?php echo "<?php\n"; ?>
/**
 * <?php echo $modelClass."\n"; ?>
 * version: 0.0.1
 *
 * @author Putra Sudaryanto <putra@sudaryanto.id>
 * @copyright Copyright (c) <?php echo date('Y'); ?> Ommu Platform (opensource.ommu.co)
 * @created date <?php echo date('j F Y, H:i')." WIB\n"; ?>
 * @link http://opensource.ommu.co
 * @contact (+62)856-299-4114
 *
 * This is the template for generating the model class of a specified table.
 * - $this: the ModelCode object
 * - $tableName: the table name for this class (prefix is already removed if necessary)
 * - $modelClass: the model class name
 * - $columns: list of table columns (name=>CDbColumnSchema)
 * - $labels: list of attribute labels (name=>label)
 * - $rules: list of validation rules
 * - $relations: list of relations (name=>relation declaration)
 *
 * --------------------------------------------------------------------------------------
 *
 * This is the model class for table "<?php echo $tableName; ?>".
 *
 * The followings are the available columns in table '<?php echo $tableName; ?>':
<?php foreach($columns as $column): ?>
 * @property <?php echo $column->type.' $'.$column->name."\n"; ?>
<?php endforeach; ?>
<?php if(!empty($relations)): ?>
 *
 * The followings are the available model relations:
<?php 
	/*
	echo '<pre>';
	print_r($relations);
	echo '</pre>';
	*/
	
foreach($relations as $name=>$relation): ?>
 * @property <?php
	if (preg_match("~^array\(self::([^,]+), '([^']+)', '([^']+)'\)$~", $relation, $matches))
    {
        $relationType = $matches[1];
        $relationModel = preg_replace('(Ommu)', '', $matches[2]);
		$name = preg_replace('(ommu_)', '', setRelationName($name));
		if($name == 'cat')
			$name = 'category';

        switch($relationType){
            case 'HAS_ONE':
                echo $relationModel.' $'.$name."\n";
            break;
            case 'BELONGS_TO':
                echo $relationModel.' $'.$name."\n";
            break;
            case 'HAS_MANY':
                echo $relationModel.'[] $'.$name."\n";
            break;
            case 'MANY_MANY':
                echo $relationModel.'[] $'.$name."\n";
            break;
            default:
                echo 'mixed $'.$name."\n";
        }
	}
foreach($labels as $name=>$label):
	if(in_array($name, array('creation_id','modified_id','user_id','updated_id','member_id'))) {
		$arrayName = explode('_', $name);
		$name = $arrayName[0];
		if($name == 'cat')
			$name = 'category';
		if($name == 'member')
			echo " * @property Members[] \${$name};\n";
		else
			echo " * @property Users[] \${$name};\n";
		$publicVariable[] = $name;
	}	
endforeach;
    ?>
<?php endforeach; ?>
<?php endif; ?>
 */
class <?php echo $modelClass; ?> extends <?php echo $this->baseClass."\n"; ?>
{
	public $defaultColumns = array();

	// Variable Search	
<?php 
$publicVariable = array();
foreach($columns as $name=>$column):
	if($column->isForeignKey == '1') {
		$arrayName = explode('_', $column->name);
		$name = $arrayName[0];
		if($name == 'cat')
			$name = 'category';
		$cName = $name.'_search';
		echo "\tpublic \${$cName};\n";
		$publicVariable[] = $cName;
	}
endforeach;
foreach($labels as $name=>$label):
	if(in_array($name, array('creation_id','modified_id','user_id','updated_id','member_id'))) {
		$arrayName = explode('_', $name);
		$name = $arrayName[0];
		if($name == 'cat')
			$name = 'category';
		$name = $name.'_search';
		echo "\tpublic \${$name};\n";
		$publicVariable[] = $name;
	}	
endforeach; ?>

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return <?php echo $modelClass; ?> the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
<?php if($connectionId!='db'):?>

	/**
	 * @return CDbConnection the database connection used for this class
	 */
	public function getDbConnection()
	{
		return Yii::app()-><?php echo $connectionId ?>;
	}
<?php endif?>

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		preg_match("/dbname=([^;]+)/i", $this->dbConnection->connectionString, $matches);
		return $matches[1].'.<?php echo $tableName; ?>';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
<?php foreach($rules as $rule): ?>
			<?php echo $rule.",\n"; ?>
<?php endforeach;?>
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('<?php echo implode(', ', array_merge(array_keys($columns), $publicVariable)); ?>', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
<?php 
	/*
	echo '<pre>';
	print_r($relations);
	echo '</pre>';
	echo exit();
	*/
	foreach($relations as $name=>$relation): ?>
			<?php
			$name = preg_replace('(ommu_)', '', setRelationName($name));
			if($name == 'cat')
				$name = 'category';
			$relation = preg_replace('(Ommu)', '', $relation);
			echo "'$name' => $relation,\n"; ?>
<?php endforeach;
	foreach($columns as $name=>$column):
		if(in_array($column->name, array('creation_id','modified_id','user_id','updated_id','member_id'))) {
			$arrayName = explode('_', $column->name);
			$cRelation = $arrayName[0];
			if($column->name == 'member_id')
				echo "\t\t\t'$cRelation' => array(self::BELONGS_TO, 'Members', '{$column->name}'),\n";
			else
				echo "\t\t\t'$cRelation' => array(self::BELONGS_TO, 'Users', '{$column->name}'),\n";
		}
	endforeach;?>
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
<?php 
foreach($labels as $name=>$label):
	if(strtolower($label) == 'cat')
		$label = 'Category';
	echo "\t\t\t'$name' => Yii::t('attribute', '$label'),\n";
endforeach;
foreach($columns as $name=>$column):
	if($column->isForeignKey == '1') {
		$arrayName = explode('_', $column->name);
		$name = $arrayName[0];
		if($name == 'cat')
			$name = 'category';
		$cName = $name.'_search';
		$cLabel = ucwords(strtolower($name));
		echo "\t\t\t'$cName' => Yii::t('attribute', '$cLabel'),\n";
	}
endforeach;
foreach($labels as $name=>$label):
	if(in_array($name, array('creation_id','modified_id','user_id','updated_id','member_id'))) {
		$arrayName = explode('_', $name);
		$name = $arrayName[0];
		if($name == 'cat')
			$name = 'category';
		$name = $name.'_search';
		echo "\t\t\t'$name' => Yii::t('attribute', '$label'),\n";
	}
endforeach; ?>
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

<?php
/*
echo '<pre>';
print_r($columns);
echo '</pre>';
echo exit();
*/
$isPrimaryKey = '';
$isVariableSearch = 0;

foreach($columns as $name=>$column) {	
	if($column->isForeignKey == '1' || (in_array($column->name, array('creation_id','modified_id','user_id','updated_id','member_id'))))
		$isVariableSearch = 1;
}
if($isVariableSearch == 1) {?>
		// Custom Search
		$criteria->with = array(
<?php foreach($columns as $name=>$column) {	
	if($column->isForeignKey == '1' || (in_array($column->name, array('creation_id','modified_id','user_id','updated_id','member_id')))) {
		$arrayName = explode('_', $column->name);
		$cName = 'displayname';
		if($column->isForeignKey == '1')
			$cName = 'column_name_relation';
		$cRelation = $arrayName[0];
		if($cRelation == 'cat')
			$cRelation = 'category';
		if($cRelation == 'member')
			$cName = 'member_id, publish, profile_id, member_private, member_header, member_photo, short_biography';			
		echo "\t\t\t'$cRelation' => array(\n";
		echo "\t\t\t\t'alias'=>'$cRelation',\n";
		echo "\t\t\t\t'select'=>'$cName'\n";
		echo "\t\t\t),\n";
		if($column->name == 'member_id') {
		echo "\t\t\t'{$cRelation}.view' => array(\n";
			echo "\t\t\t\t'alias'=>'{$cRelation}_view',\n";
			echo "\t\t\t\t'select'=>'member_name'\n";
			echo "\t\t\t),\n";			
		}
	}
}?>
		);
		
<?php }
/*
foreach($columns as $name=>$column) {	
	if($column->isForeignKey == '1' || (in_array($column->name, array('creation_id','modified_id','user_id','updated_id','member_id')))) {
		$arrayName = explode('_', $column->name);
		$cName = 'displayname';
		if($column->isForeignKey == '1')
			$cName = 'column_name_relation';
		$cRelation = $arrayName[0];
		if($cRelation == 'cat')
			$cRelation = 'category';
		if($column->name == 'member_id') {
			$cRelation = 'member_view';
			$cName = 'member_name';	
		}
		$name = $cRelation.'_search';
		echo "\t\t\$criteria->compare('{$cRelation}.{$cName}',strtolower(\$this->$name),true);\n";
	}
}
*/
foreach($columns as $name=>$column) {
	if($column->name == 'publish') {
		echo "\t\tif(isset(\$_GET['type']) && \$_GET['type'] == 'publish')\n";
		echo "\t\t\t\$criteria->compare('t.$name',1);\n";
		echo "\t\telseif(isset(\$_GET['type']) && \$_GET['type'] == 'unpublish')\n";
		echo "\t\t\t\$criteria->compare('t.$name',0);\n";
		echo "\t\telseif(isset(\$_GET['type']) && \$_GET['type'] == 'trash')\n";
		echo "\t\t\t\$criteria->compare('t.$name',2);\n";
		echo "\t\telse {\n";
		echo "\t\t\t\$criteria->addInCondition('t.$name',array(0,1));\n";
		echo "\t\t\t\$criteria->compare('t.$name',\$this->$name);\n";
		echo "\t\t}\n";

	} else if($column->isForeignKey == '1' || (in_array($column->name, array('creation_id','modified_id','user_id','updated_id','member_id')))) {
		$arrayName = explode('_', $column->name);
		$cName = $arrayName[0];
		if($cName == 'cat')
			$cName = 'category';
		echo "\t\tif(isset(\$_GET['$cName']))\n";
		echo "\t\t\t\$criteria->compare('t.$name',\$_GET['$cName']);\n";
		echo "\t\telse\n";
		echo "\t\t\t\$criteria->compare('t.$name',\$this->$name);\n";

	} else if(in_array($column->dbType, array('timestamp','datetime','date'))) {
		echo "\t\tif(\$this->$name != null && !in_array(\$this->$name, array('0000-00-00 00:00:00', '0000-00-00')))\n";
		echo "\t\t\t\$criteria->compare('date(t.$name)',date('Y-m-d', strtotime(\$this->$name)));\n";

	} else if(in_array($column->dbType, array('int','smallint'))) {
		echo "\t\t\$criteria->compare('t.$name',\$this->$name);\n";

	} else if($column->type==='string') {
		echo "\t\t\$criteria->compare('t.$name',strtolower(\$this->$name),true);\n";

	} else {
		echo "\t\t\$criteria->compare('t.$name',\$this->$name);\n";

	}
	if($column->isPrimaryKey) {
		$isPrimaryKey = $name;
	}
}
if($isVariableSearch == 1)
	echo "\n";
foreach($columns as $name=>$column) {	
	if($column->isForeignKey == '1' || (in_array($column->name, array('creation_id','modified_id','user_id','updated_id','member_id')))) {
		$arrayName = explode('_', $column->name);
		$cName = 'displayname';
		if($column->isForeignKey == '1')
			$cName = 'column_name_relation';
		$cRelation = $arrayName[0];
		if($cRelation == 'cat')
			$cRelation = 'category';
		$name = $cRelation.'_search';
		if($column->name == 'member_id') {
			$cRelation = 'member_view';
			$cName = 'member_name';	
		}
		echo "\t\t\$criteria->compare('{$cRelation}.{$cName}',strtolower(\$this->$name),true);\n";
	}
}
	echo "\n\t\tif(!isset(\$_GET['{$modelClass}_sort']))\n";
	echo "\t\t\t\$criteria->order = 't.$isPrimaryKey DESC';\n";
?>

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>30,
			),
		));
	}

	/**
	 * Get column for CGrid View
	 */
	public function getGridColumn($columns=null) {
		if($columns !== null) {
			foreach($columns as $val) {
				/*
				if(trim($val) == 'enabled') {
					$this->defaultColumns[] = array(
						'name'  => 'enabled',
						'value' => '$data->enabled == 1? "Ya": "Tidak"',
					);
				}
				*/
				$this->defaultColumns[] = $val;
			}
		} else {
<?php
foreach($columns as $name=>$column)
{
	if($column->isPrimaryKey)
		echo "\t\t\t"."//"."\$this->defaultColumns[] = '$name';\n";
	else
		echo "\t\t\t\$this->defaultColumns[] = '$name';\n";
}
?>
		}

		return $this->defaultColumns;
	}

	/**
	 * Set default columns to display
	 */
	protected function afterConstruct() {
		if(count($this->defaultColumns) == 0) {
			/*
			$this->defaultColumns[] = array(
				'class' => 'CCheckBoxColumn',
				'name' => 'id',
				'selectableRows' => 2,
				'checkBoxHtmlOptions' => array('name' => 'trash_id[]')
			);
			*/
			$this->defaultColumns[] = array(
				'header' => 'No',
				'value' => '$this->grid->dataProvider->pagination->currentPage*$this->grid->dataProvider->pagination->pageSize + $row+1'
			);
<?php
foreach($columns as $name=>$column)
{
	if(!$column->isPrimaryKey) {
		if($column->dbType == 'tinyint(1)') {
			if(in_array($column->name, array('publish')))
				echo "\t\t\tif(!isset(\$_GET['type'])) {\n";
			echo "\t\t\t\$this->defaultColumns[] = array(\n";
			echo "\t\t\t\t'name' => '$name',\n";
			echo "\t\t\t\t'value' => 'Utility::getPublish(Yii::app()->controller->createUrl(\'$name\',array(\'id\'=>\$data->$isPrimaryKey)), \$data->$name)',\n";
			echo "\t\t\t\t'htmlOptions' => array(\n";
			echo "\t\t\t\t\t'class' => 'center',\n";
			echo "\t\t\t\t),\n";
			echo "\t\t\t\t'filter'=>array(\n";
			echo "\t\t\t\t\t1=>Yii::t('phrase', 'Yes'),\n";
			echo "\t\t\t\t\t0=>Yii::t('phrase', 'No'),\n";
			echo "\t\t\t\t),\n";
			echo "\t\t\t\t'type' => 'raw',\n";
			echo "\t\t\t);\n";
			if(in_array($column->name, array('publish')))
				echo "\t\t\t}\n";
			
		} else if($column->isForeignKey == '1' || (in_array($column->name, array('creation_id','modified_id','user_id','updated_id','member_id')))) {
			$arrayName = explode('_', $column->name);
			$cName = 'displayname';
			if($column->isForeignKey == '1')
				$cName = 'column_name_relation';
			$cRelation = $arrayName[0];
			if($cRelation == 'cat')
				$cRelation = 'category';
			$name = $cRelation.'_search';
			if($column->name == 'member_id') {
				$cRelation = 'member_view';
				$cName = 'member_name';	
			}
			echo "\t\t\tif(!isset(\$_GET['$cRelation'])) {\n";
			echo "\t\t\t\$this->defaultColumns[] = array(\n";
			echo "\t\t\t\t'name' => '$name',\n";
			echo "\t\t\t\t'value' => '\$data->{$cRelation}->{$cName}',\n";
			echo "\t\t\t);\n";
			echo "\t\t\t}\n";
			
		} else if(in_array($column->dbType, array('timestamp','datetime','date'))) {
			echo "\t\t\t\$this->defaultColumns[] = array(\n";
			echo "\t\t\t\t'name' => '$name',\n";
			echo "\t\t\t\t'value' => 'Utility::dateFormat(\$data->$name)',\n";
			echo "\t\t\t\t'htmlOptions' => array(\n";
			echo "\t\t\t\t\t'class' => 'center',\n";
			echo "\t\t\t\t),\n";
			echo "\t\t\t\t'filter' => Yii::app()->controller->widget('application.components.system.CJuiDatePicker', array(\n";
			echo "\t\t\t\t\t'model'=>\$this,\n";
			echo "\t\t\t\t\t'attribute'=>'$name',\n";
			echo "\t\t\t\t\t'language' => 'en',\n";
			echo "\t\t\t\t\t'i18nScriptFile' => 'jquery-ui-i18n.min.js',\n";
			echo "\t\t\t\t\t//'mode'=>'datetime',\n";
			echo "\t\t\t\t\t'htmlOptions' => array(\n";
			echo "\t\t\t\t\t\t'id' => '$name";echo "_filter',\n";
			echo "\t\t\t\t\t),\n";
			echo "\t\t\t\t\t'options'=>array(\n";
			echo "\t\t\t\t\t\t'showOn' => 'focus',\n";
			echo "\t\t\t\t\t\t'dateFormat' => 'dd-mm-yy',\n";
			echo "\t\t\t\t\t\t'showOtherMonths' => true,\n";
			echo "\t\t\t\t\t\t'selectOtherMonths' => true,\n";
			echo "\t\t\t\t\t\t'changeMonth' => true,\n";
			echo "\t\t\t\t\t\t'changeYear' => true,\n";
			echo "\t\t\t\t\t\t'showButtonPanel' => true,\n";
			echo "\t\t\t\t\t),\n";
			echo "\t\t\t\t), true),\n";
			echo "\t\t\t);\n";
			
		} else {
			echo "\t\t\t\$this->defaultColumns[] = array(\n";
			echo "\t\t\t\t'name' => '$name',\n";
			echo "\t\t\t\t'value' => '\$data->$name',\n";
			echo "\t\t\t);\n";
		}
	}
}
?>
		}
		parent::afterConstruct();
	}

	/**
	 * User get information
	 */
	public static function getInfo($id, $column=null)
	{
		if($column != null) {
			$model = self::model()->findByPk($id,array(
				'select' => $column
			));
			return $model->$column;
			
		} else {
			$model = self::model()->findByPk($id);
			return $model;			
		}
	}

	/**
	 * before validate attributes
	 */
	protected function beforeValidate() 
	{
		if(parent::beforeValidate()) {
<?php
foreach($columns as $name=>$column)
{
	if(in_array($column->name, array('creation_id','modified_id','updated_id')) && $column->comment != 'trigger') {
		if($column->name == 'creation_id') {
			echo "\t\t\tif(\$this->isNewRecord)\n";
			echo "\t\t\t\t\$this->{$column->name} = Yii::app()->user->id;\n";
		} else {
			echo "\t\t\telse\n";
			echo "\t\t\t\t\$this->{$column->name} = Yii::app()->user->id;\n";			
		}
	}
}
?>
		}
		return true;
	}

	/**
	 * after validate attributes
	 */
	protected function afterValidate()
	{
		parent::afterValidate();
		// Create action
		
		return true;
	}
	
	/**
	 * before save attributes
	 */
	protected function beforeSave() 
	{
		if(parent::beforeSave()) {
			// Create action
<?php
foreach($columns as $name=>$column)
{
	if(in_array($column->dbType, array('date')) && $column->comment != 'trigger') {
		echo "\t\t\t//\$this->$name = date('Y-m-d', strtotime(\$this->$name));\n";
	}
}
?>
		}
		return true;	
	}
	
	/**
	 * After save attributes
	 */
	protected function afterSave() 
	{
		parent::afterSave();
		// Create action
	}

	/**
	 * Before delete attributes
	 */
	protected function beforeDelete() 
	{
		if(parent::beforeDelete()) {
			// Create action
		}
		return true;
	}

	/**
	 * After delete attributes
	 */
	protected function afterDelete() 
	{
		parent::afterDelete();
		// Create action
	}

}