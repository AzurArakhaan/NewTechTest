<?php

namespace App\Models;

use App\Exceptions\Handler;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    use HasFactory;

    /**
     * Добавляет новые лэйблы переданной сущности
     *
     * @param string $entityType
     * @param int $entityID
     * @param array $labels
     * @throws \Exception
     */
    public static function addLabels($entityType, $entityID, $labels)
    {
        try {
            self::checkIsEmptyArray($labels);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        try {
            self::checkIsEmptyEntity($entityType, $entityID);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        foreach ($labels as $label) {
            self::insert([
                'entity_type' => $entityType,
                'entity_id' => $entityID,
                'value' => $label
            ]);
        }
    }

    /**
     * Перезаписывает лэйблы переданной сущности
     * Записывает переданный массив строк как новые лэйблы, старые удаляет
     *
     * @param string $entityType
     * @param int $entityID
     * @param array $labels
     * @throws \Exception
     */
    public static function rewriteLabels($entityType, $entityID, $labels)
    {
        try {
            self::checkIsEmptyEntity($entityType, $entityID);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        self::where('entity_type', '=', $entityType)
            ->where('entity_id', '=', $entityID)
            ->delete();

        foreach ($labels as $label) {
            self::insert([
                'entity_type' => $entityType,
                'entity_id' => $entityID,
                'value' => $label
            ]);
        }
    }

    /**
     * Удаляет переданные лэйблы у переданной сущности
     * Если какого-либо из переданных лэйблов не существует - возвращает ошибку
     *
     * @param string $entityType
     * @param int $entityID
     * @param array $labels
     * @throws \Exception
     */
    public static function deleteLabels($entityType, $entityID, $labels)
    {
        try {
            self::checkIsEmptyArray($labels);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        foreach ($labels as $label) {
            $dbLabel = self::where('entity_type', '=', $entityType)
                ->where('entity_id', '=', $entityID)
                ->where('value', '=', $label);

            try {
                $dbLabel->firstOrFail();
                $dbLabel->delete();
            } catch (\Exception $e) {
                throw new \Exception('Переданный лэйбл не найден в БД');
            }
        }
    }

    /**
     * Возвращает лэйблы переданной сущности
     *
     * @param string $entityType
     * @param int $entityID
     * @return mixed
     */
    public static function getLabels($entityType, $entityID)
    {
        return self::where('entity_type', '=', $entityType)
            ->where('entity_id', '=', $entityID)
            ->get();
    }

    /**
     * Проверяет пустой ли переданный массив.
     * Если пустой - пробрасывает ошибку.
     *
     * @param $array
     * @throws \Exception
     */
    private static function checkIsEmptyArray($array)
    {
        if (count($array) == 0) {
            throw new \Exception('Нельзя передавать пустой массив');
        }
    }

    /**
     * Проверяет существует ли указанная сущность.
     * Если нет - пробрасывает ошибку.
     *
     * @param $entityType
     * @param $entityID
     * @throws \Exception
     */
    private static function checkIsEmptyEntity($entityType, $entityID)
    {
        try {
            Entity::where('entity_type','=',$entityType)
                ->where('id','=',$entityID)
                ->firstOrFail();
        } catch (\Exception $e) {
            throw new \Exception('Указанной сущности не существует');
        }
    }
}
