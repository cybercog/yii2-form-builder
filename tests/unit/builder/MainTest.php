<?php
/**
 * MainTest.php
 * @author Revin Roman http://phptime.ru
 */

namespace unit\builder;

use unit\TestCase;
use yii\base\DynamicModel;
use yii\widgets\ActiveForm;

/**
 * Class MainTest
 */
class MainTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $config = $this->loadConfig();
        $this->mockApplication($config);
    }

    // Yii2 default Form tests
    public function assertEqualsWithoutLE($expected, $actual)
    {
        $expected = str_replace("\r\n", "\n", $expected);
        $actual = str_replace("\r\n", "\n", $actual);

        $this->assertEquals($expected, $actual);
    }

    public function testBooleanAttributes()
    {
        $o = ['template' => '{input}'];

        $model = new DynamicModel(['name']);
        ob_start();
        $form = new ActiveForm(['action' => '/something']);
        ob_end_clean();

        $this->assertEqualsWithoutLE(<<<EOF
<div class="form-group field-dynamicmodel-name">
<input type="email" id="dynamicmodel-name" class="form-control" name="DynamicModel[name]" required>
</div>
EOF
            , (string) $form->field($model, 'name', $o)->input('email', ['required' => true]));

        $this->assertEqualsWithoutLE(<<<EOF
<div class="form-group field-dynamicmodel-name">
<input type="email" id="dynamicmodel-name" class="form-control" name="DynamicModel[name]">
</div>
EOF
            , (string) $form->field($model, 'name', $o)->input('email', ['required' => false]));


        $this->assertEqualsWithoutLE(<<<EOF
<div class="form-group field-dynamicmodel-name">
<input type="email" id="dynamicmodel-name" class="form-control" name="DynamicModel[name]" required="test">
</div>
EOF
            , (string) $form->field($model, 'name', $o)->input('email', ['required' => 'test']));

    }

    public function testIssue5356()
    {
        $o = ['template' => '{input}'];

        $model = new DynamicModel(['categories']);
        $model->categories = 1;
        ob_start();
        $form = new ActiveForm(['action' => '/something']);
        ob_end_clean();

        // https://github.com/yiisoft/yii2/issues/5356
        $this->assertEqualsWithoutLE(<<<EOF
<div class="form-group field-dynamicmodel-categories">
<input type="hidden" name="DynamicModel[categories]" value=""><select id="dynamicmodel-categories" class="form-control" name="DynamicModel[categories][]" multiple size="4">
<option value="0">apple</option>
<option value="1" selected>banana</option>
<option value="2">avocado</option>
</select>
</div>
EOF
            , (string) $form->field($model, 'categories', $o)->listBox(['apple', 'banana', 'avocado'], ['multiple' => true]));
    }
}
