<?php
declare(strict_types=1);

namespace Ria\Bundle\DataGridBundle\Hydrators;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

class DataGridHydrator extends AbstractHydrator
{
    protected function hydrateAllData(): array
    {
        $result = array();
        while ($data = $this->_stmt->fetch(\PDO::FETCH_ASSOC)) {
            $this->hydrateRowData($data, $result);
        }
        return $result;
    }

    protected function hydrateRowData(array $row, array &$result): void
    {
        $result[] = $this->gatherScalarRowData($row);
    }

    protected function gatherScalarRowData(&$data): array
    {
        $rowData = array();
        foreach ($data as $key => $value) {
            if (($cacheKeyInfo = $this->hydrateColumnInfo($key)) === null) {
                continue;
            }
            $fieldName = $cacheKeyInfo['fieldName'];
            // WARNING: BC break! We know this is the desired behavior to type convert values, but this
            // erroneous behavior exists since 2.0 and we're forced to keep compatibility.
            if ( ! isset($cacheKeyInfo['isScalar'])) {
                $dqlAlias  = $cacheKeyInfo['dqlAlias'];
                $type      = $cacheKeyInfo['type'];
                $fieldName = $dqlAlias . $this->getFieldSeparator() . $fieldName;
                $value     = $type
                    ? $type->convertToPHPValue($value, $this->_platform)
                    : $value;
            }
            $rowData[$fieldName] = $value;
        }
        return $rowData;
    }

    protected function getFieldSeparator(): string
    {
        return '.';
    }
}
