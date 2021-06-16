<?php
namespace Ria\Bundle\DataGridBundle\Grid;

use Ria\Bundle\DataGridBundle\DataGridException;

class Field
{
    /** @var string */
    protected $fieldName;
    /** @var string */
    protected $label;
    /** @var boolean */
    protected $sortable = false;
    /** @var boolean */
    protected $filterable = false;
    /** @var boolean */
    protected $visible = true;
    /** @var callable */
    protected $formatValueCallback;
    /** @var boolean */
    protected $autoEscape = true;
    /** @var boolean */
    protected $translatable = false;
    /** @var string */
    protected $category;
    /** @var bool */
    protected $nullIfNotExists = false;
    /** @var array  */
    protected $dataList = array();
    /** @var string */
    protected $uniqueId;
    /** @var array  */
    protected $attr = array();
    /** @var array  */
    protected $filterAttr = array();

    protected array $filterData;

    /**
     * List of tags associated to a field. Used only by users of the bundles.
     * No influence in the internals of the bundle.
     * @var string[]
     */
    protected $tagList = [];

    public function __construct(
        $fieldName,
        array $optionList = array(),
        array $tagList = array()
    ) {
        $this->fieldName = $fieldName;
        $this->label = $fieldName;
        foreach ($optionList as $key => $val) {
            if (\in_array($key, array(
                'label',
                'sortable',
                'filterable',
                'visible',
                'formatValueCallback',
                'autoEscape',
                'translatable',
                'category',
                'nullIfNotExists',
                'dataList',
                'uniqueId',
                'filterData',
                'attr',
                'filterAttr'
            ), true)) {
                $this->$key = $val;
            } else {
                throw new \InvalidArgumentException("key $key doesn't exist in option list");
            }
        }
        $this->tagList = $tagList;
    }

    /**
     * @param string $fieldName
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @param boolean $filterable
     */
    public function setFilterable($filterable)
    {
        $this->filterable = $filterable;
    }

    /**
     * @return boolean
     */
    public function getFilterable()
    {
        return $this->filterable;
    }

    /**
     * @param callable $formatValueCallback
     */
    public function setFormatValueCallback($formatValueCallback)
    {
        $this->formatValueCallback = $formatValueCallback;
    }

    /**
     * @return callable
     */
    public function getFormatValueCallback()
    {
        return $this->formatValueCallback;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param boolean $sortable
     */
    public function setSortable($sortable)
    {
        $this->sortable = $sortable;
    }

    /**
     * @return boolean
     */
    public function getSortable()
    {
        return $this->sortable;
    }

    /**
     * @param boolean $visible
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    }

    /**
     * @return boolean
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * @param boolean $autoEscape
     */
    public function setAutoEscape($autoEscape)
    {
        $this->autoEscape = $autoEscape;
    }

    /**
     * @return boolean
     */
    public function getAutoEscape()
    {
        return $this->autoEscape;
    }

    /**
     * @param boolean $translatable
     */
    public function setTranslatable($translatable)
    {
        $this->translatable = $translatable;
    }

    /**
     * @return boolean
     */
    public function getTranslatable()
    {
        return $this->translatable;
    }

    /**
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param boolean $nullIfNotExists
     */
    public function setNullIfNotExists($nullIfNotExists)
    {
        $this->nullIfNotExists = $nullIfNotExists;
    }

    /**
     * @return boolean
     */
    public function getNullIfNotExists()
    {
        return $this->nullIfNotExists;
    }

    /**
     * @param string $key
     * @return array
     * @throws DataGridException
     */
    public function getData($key)
    {
        if (!array_key_exists($key, $this->dataList)) {
            throw new DataGridException(
                "key [$key] is not defined in the data-list (should be defined in the dataList parameter in the new Field..."
            );
        }
        return $this->dataList[$key];
    }

    /**
     * @return string[]
     */
    public function getTagList()
    {
        return $this->tagList;
    }

    /**
     * @param string[] $tagList
     * @return self
     */
    public function setTagList($tagList)
    {
        $this->tagList = $tagList;
        return $this;
    }

    /**
     * Returns true if the given $tag is present in the tag list of the field.
     *
     * @param $tag
     * @return bool
     */
    public function hasTag($tag)
    {
        return \in_array($tag, $this->tagList, true);
    }

    /**
     * @return string
     */
    public function getUniqueId(): ?string
    {
        return $this->uniqueId;
    }

    /**
     * @param string $uniqueId
     */
    public function setUniqueId(string $uniqueId): void
    {
        $this->uniqueId = $uniqueId;
    }


    public function getFilterData(): array
    {
        return $this->filterData;
    }

    public function setFilterData(array $filterData): void
    {
        $this->filterData = $filterData;
    }

    public function hasFilterData(): bool
    {
        return !empty($this->filterData);
    }

    /**
     * @return array
     */
    public function getFilterAttr(): array
    {
        return $this->filterAttr;
    }

    /**
     * @param array $filterAttr
     */
    public function setFilterAttr(array $filterAttr): void
    {
        $this->filterAttr = $filterAttr;
    }

    /**
     * @return array
     */
    public function getAttr(): array
    {
        return $this->attr;
    }

    /**
     * @param array $attr
     */
    public function setAttr(array $attr): void
    {
        $this->attr = $attr;
    }
}
