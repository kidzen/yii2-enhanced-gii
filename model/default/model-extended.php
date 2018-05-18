<?php
/**
 * This is the template for generating the model class of a specified table.
 *
 * @var yii\web\View $this
 * @var mootensai\enhancedgii\model\Generator $generator
 * @var string $tableName full table name
 * @var string $className class name
 * @var yii\db\TableSchema $tableSchema
 * @var string[] $labels list of attribute labels (name => label)
 * @var string[] $rules list of validation rules
 * @var array $relations list of relations (name => relation declaration)
 */

echo "<?php\n";
?>

namespace <?= $generator->nsModel ?>;

use Yii;
use \<?= $generator->nsModel ?>\base\<?= $className ?> as Base<?= $className ?>;

/**
 * This is the model class for table "<?= $tableName ?>".
 */
class <?= $className ?> extends Base<?= $className . "\n" ?>
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [<?= "\n            " . implode(",\n            ", $rules) . "\n        " ?>]
        );
    }

<?php if ($generator->generateAttributeHints): ?>
    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
<?php foreach ($labels as $name => $label): ?>
<?php if (!in_array($name, $generator->skippedColumns)): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endif; ?>
<?php endforeach; ?>
        ];
    }
<?php endif; ?>

    /**
     * @inheritdoc
     */
    /* public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        // add code here. given sample code
        if ($insert === false) {
            return; // only work with newly created payments
        }

        if ($this->credit->save(false) === false) {
            throw new Exception("credit couldn't be update");
        }
    } */

    /**
     * @inheritdoc
     */
    /* public function beforeSave($insert)
    {
        // custom code here
        return parent::beforeSave($insert);
    } */

    public static function arrayList()
    {
        $query = self::find()->select('id,name')->asArray()->all();
        $callback = function($data) {
            return $data['name'];
        };

        $result = \yii\helpers\ArrayHelper::map($query, 'id', $callback);
        return $result;
    }
}
