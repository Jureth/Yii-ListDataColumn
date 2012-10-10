<?php

/**
 * Столбец таблицы, выдающий список значений из отношения AR.
 *
 * @package baseclass_extensions
 * @subpackage CDataColumn
 * @author Yuri 'Jureth' Minin <JurethInterior@yandex.ru>
 * @version v1.1,2012/10/10
 */
class listDataColumn extends CDataColumn {

    /**
     * Основной тег списка. Может быть 'ul' либо 'ol'
     * @var string
     */
    public $listType = 'ul'; //ul or ol
    /**
     * HTML-параметры элементов (li) списка.
     * @var array
     */
    public $itemHtmlOptions = array( );
    /**
     * HTML-параметры списка
     * @var array
     */
    public $htmlOptions = array( );
    /**
     * Свойство модели, возвращающее массив(список) моделей. Может быть отношением
     * HAS_MANY или MANY_MANY, а также просто свойством, возвращающем массив классов
     * @var string
     */
    public $name;
    /**
     * Имя свойства для отображения моделей из списка.
     * @var string
     */
    public $field;
    /**
     * PHP-код для вывода произвольной информации как элемента списка
     * @var string
     */
    public $valueExpression = NULL;
    /**
     * Код фильтра столбца
     * @var string
     */
    public $filter;

    protected function renderFilterCellContent(){
        if ( $this->filter !== false && $this->grid->filter !== null && strpos($this->name, '.') === false ){
            if ( is_array($this->filter) ) {
                echo CHtml::activeDropDownList(
                    $this->grid->filter,
                    $this->name,
                    $this->filter,
                    array( 'id' => false, 'prompt' => '' )
                );
            }else{
                echo $this->filter;
            }
        }else{
            parent::renderFilterCellContent();
        }
    }

    protected function renderDataCellContent($row, $data){

        if ( !in_array($this->listType, array( 'ul', 'ol' )) ){
            throw new Exception('list type is not valid');
        }
        if ( !$this->name ){
            throw new exception('Name not defined');
        }

        $field = $this->field;
        $list = CHtml::value($data, $this->name);

        if ( !is_array($list) ){
            return;
        } //throw new exception('Field '.$this->name. ' is not an array!');

        $result = CHtml::tag($this->listType, $this->htmlOptions, false, false);

        foreach( $list as $value ){
            if ( $this->valueExpression !== NULL ){
                $content = $this->evaluateExpression(
                    $this->valueExpression,
                    array( 'data' => $value, 'row' => $row )
                );
            }else{
                $content = CHtml::value($value, $field);
            }
            $result .= CHtml::tag('li', $this->itemHtmlOptions, $content, true);
        }

        $result .= CHtml::closeTag($this->listType);
        echo $result;
    }
}

