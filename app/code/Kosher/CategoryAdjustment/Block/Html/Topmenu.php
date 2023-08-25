<?php
declare(strict_types=1);

namespace Kosher\CategoryAdjustment\Block\Html;

use Kosher\CategoryAdjustment\Query\GetCategoryIconPathQuery;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategotyCollectionFactory;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree\Node\Collection;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Data\TreeFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template\Context;
use Magento\Theme\Block\Html\Topmenu as ThemeTopmenu;

class Topmenu extends ThemeTopmenu
{
    /**
     * @var CategotyCollectionFactory
     */
    private CategotyCollectionFactory $categotyCollectionFactory;

    /**
     * @var GetCategoryIconPathQuery
     */
    private GetCategoryIconPathQuery $categoryIconPathQuery;

    /**
     * @param Context $context
     * @param NodeFactory $nodeFactory
     * @param TreeFactory $treeFactory
     * @param CategotyCollectionFactory $categotyCollectionFactory
     * @param GetCategoryIconPathQuery $categoryIconPathQuery
     * @param array $data
     */
    public function __construct(
        Context $context,
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        CategotyCollectionFactory $categotyCollectionFactory,
        GetCategoryIconPathQuery $categoryIconPathQuery,
        array $data = []
    ) {
        $this->categotyCollectionFactory = $categotyCollectionFactory;
        $this->categoryIconPathQuery = $categoryIconPathQuery;
        parent::__construct($context, $nodeFactory, $treeFactory, $data);
    }

    /**
     * {@inheritdoc}
     * @throws LocalizedException
     */
    protected function _getHtml(Node $menuTree, $childrenWrapClass, $limit, array $colBrakes = []): string
    {
        $html = '';

        $children = $menuTree->getChildren();
        $childLevel = $this->getChildLevel($menuTree->getLevel());
        $this->removeChildrenWithoutActiveParent($children, $childLevel);

        $counter = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

        /** @var Node $child */
        foreach ($children as $child) {
            $child->setLevel($childLevel);
            $child->setIsFirst($counter === 1);
            $child->setIsLast($counter === $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel === 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $this->setCurrentClass($child, $outermostClass);
            }

            if ($this->shouldAddNewColumn($colBrakes, $counter)) {
                $html .= '</ul></li><li class="column"><ul>';
            }


            $categoryName = $child->getName();
            $pathAttributeIcon = $this->getCategoryIconPathByTitle($categoryName);
            $attributeIconClass = 'category-icon';
            $attributeIcon = '';
            if ($pathAttributeIcon) {
                $attributeIcon = '<div class="' .
                    $attributeIconClass . '">' .
                    '<img alt="" src="' . $pathAttributeIcon . '">' .
                    '</div>';
            }

            $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
            $html .= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '>' . $attributeIcon . '<span>' . $this->escapeHtml(
                    $child->getName()
                ) . '</span></a>' . $this->_addSubMenu(
                    $child,
                    $childLevel,
                    $childrenWrapClass,
                    $limit
                ) . '</li>';
            $counter++;
        }

        if (is_array($colBrakes) && !empty($colBrakes) && $limit) {
            $html = '<li class="column"><ul>' . $html . '</ul></li>';
        }

        return $html;
    }

    /**
     * @param Collection $children
     * @param int $childLevel
     * @return void
     */
    private function removeChildrenWithoutActiveParent(Collection $children, int $childLevel): void
    {
        /** @var Node $child */
        foreach ($children as $child) {
            if ($childLevel === 0 && $child->getData('is_parent_active') === false) {
                $children->delete($child);
            }
        }
    }

    /**
     * @param $parentLevel
     * @return int
     */
    private function getChildLevel($parentLevel): int
    {
        return $parentLevel === null ? 0 : $parentLevel + 1;
    }

    /**
     * @param array $colBrakes
     * @param int $counter
     * @return bool
     */
    private function shouldAddNewColumn(array $colBrakes, int $counter): bool
    {
        return count($colBrakes) && $colBrakes[$counter]['colbrake'];
    }

    /**
     * @param string $categoryTitle
     * @return string|null
     * @throws LocalizedException
     */
    private function getCategoryIconPathByTitle(string $categoryTitle): ?string
    {
        $category = $this->categotyCollectionFactory->create()
            ->addAttributeToFilter('name', $categoryTitle)
            ->setPageSize(10);

        if ($category->getSize()) {
            $categoryAttributeId = (int)$category->getAttribute('category_icon')->getAttributeId();
            $categoryList = $category->getItems();
            foreach ($categoryList as $data) {
                $categoryId = (int)$data->getEntityId();
                $iconPath = $this->categoryIconPathQuery->execute($categoryAttributeId, $categoryId);
                if ($iconPath) {
                    return $iconPath;
                }
            }
        }

        return null;
    }
}
