<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator \mootensai\enhancedgii\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();
$tableSchema = $generator->getTableSchema();
$baseModelClass = StringHelper::basename($generator->modelClass);
$fk = $generator->generateFK($tableSchema);
echo "<?php\n";
?>

/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

use kartik\helpers\Html;
use kartik\export\ExportMenu;
use kartik\dynagrid\DynaGrid;
use <?= $generator->indexWidgetType === 'grid' ? "kartik\\grid\\GridView;" : "yii\\widgets\\ListView;" ?>


$this->title = <?= ($generator->pluralize) ? $generator->generateString(Inflector::pluralize(Inflector::camel2words($baseModelClass))) : $generator->generateString(Inflector::camel2words($baseModelClass)) ?>;
$this->params['breadcrumbs'][] = $this->title;
$search = "$('.search-button').click(function(){
	$('.search-form').toggle(1000);
	return false;
});";
$this->registerJs($search);
?>
<div class="<?= Inflector::camel2id($baseModelClass) ?>-index">

<?php if (!empty($generator->searchModelClass)): ?>
    <div class="search-form" style="display:none">
        <?= "<?= " ?> $this->render('_search', ['model' => $searchModel]); ?>
    </div>
    <?php endif; ?>
<?php
if ($generator->indexWidgetType === 'grid'):
?>
<?= "<?php \n" ?>
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
<?php
    if ($generator->expandable && !empty($fk)):
?>
        [
            'class' => 'kartik\grid\ExpandRowColumn',
            'width' => '50px',
            'value' => function ($model, $key, $index, $column) {
                return GridView::ROW_COLLAPSED;
            },
            'detail' => function ($model, $key, $index, $column) {
                return Yii::$app->controller->renderPartial('_expand', ['model' => $model]);
            },
            'headerOptions' => ['class' => 'kartik-sheet-style'],
            'expandOneOnly' => true,
            'visible' => false
        ],
<?php
    endif;
?>
<?php
    if ($tableSchema === false) :
        foreach ($generator->getColumnNames() as $name) {
            if (++$count < 6) {
                echo "            '" . $name . "',\n";
            } else {
                echo "            // '" . $name . "',\n";
            }
        }
    else :
        foreach ($tableSchema->getColumnNames() as $attribute):
            if (!in_array($attribute, $generator->skippedColumns)) :
?>
        <?= $generator->generateGridViewFieldIndex($attribute, $fk, $tableSchema)?>
<?php
            endif;
        endforeach; ?>
        [
            'class' => 'yii\grid\ActionColumn',
<?php if($generator->saveAsNew): ?>
            'template' => '{save-as-new} {view} {update} {delete}',
            'buttons' => [
                'save-as-new' => function ($url) {
                    return Html::a('<span class="glyphicon glyphicon-copy"></span>', $url, ['title' => 'Save As New']);
                },
            ],
<?php endif; ?>
        ],
    ];
<?php
    endif;
?>
    ?>
    <?= "<?= " ?>DynaGrid::widget([
        'columns'=>$gridColumn,
        'storage'=>DynaGrid::TYPE_COOKIE,
        'theme'=>'panel-danger',
        'showPersonalize'=>true,
        'gridOptions'=>[
            'dataProvider' => $dataProvider,
            <?= !empty($generator->searchModelClass) ? "'filterModel' => \$searchModel,\n" : ""; ?>
            'filterSelector' => 'select[name="per-page"]',
            // 'showPageSummary'=>true,
            //'floatHeader'=>true,
            'responsiveWrap'=>false,
            'pjax' => true,
            'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-<?= Inflector::camel2id(StringHelper::basename($generator->modelClass))?>']],
            'panel' => [
                'heading' => '<span class="glyphicon glyphicon-book"></span>  ' . Html::encode($this->title),
                'before' =>  '<div style="padding-top: 7px;"><em>* The table header sticks to the top in this demo as you scroll</em></div>',
                'after' => false,
            ],
<?php if(!$generator->pdf) : ?>
            // 'export' => false,
<?php endif; ?>
            // your toolbar can include the additional full export menu
            'toolbar' => [
                ['content'=>
                    Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'], ['class' => 'btn btn-success', 'title'=><?= $generator->generateString('Create ' . Inflector::camel2words($baseModelClass)) ?>]) . ' '.
<?php if (!empty($generator->searchModelClass)): ?>
                    Html::a(<?= $generator->generateString('Advance Search')?>, '#', ['class' => 'btn btn-info search-button'])
<?php endif; ?>
                ],
                ['content'=>'{dynagridFilter}{dynagridSort}{dynagrid}'],
                '{export}',
                '{toggleData}',
<?php if(!$generator->pdf):?>
                'exportConfig' => [
                    // ExportMenu::FORMAT_PDF => false
                ]
<?php endif;?>
            ],
        ],
        'options'=>['id'=>'dynagrid-1'] // a unique identifier is important
    ]); ?>
<?php
else:
?>
    <?= "<?= " ?>ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return $this->render('_index',['model' => $model, 'key' => $key, 'index' => $index, 'widget' => $widget, 'view' => $this]);
        },
    ]) ?>
<?php
endif;
?>

</div>
