<?php
namespace app\models;

use yii\db\ActiveQuery;

class RequestSearch extends Request
{
    /**
     *
     * @param string|null $status
     * @param string|null $date_from
     * @param string|null $date_to
     * @return array
     */
    public function search($status = null, $date_from = null, $date_to = null)
    {
        $query = Request::find();
        if ($status) {
            $query->andWhere(['status' => $status]);
        }

        if ($date_from) {
            $query->andWhere(['>=', 'created_at', $date_from . ' 00:00:00']);
        }

        if ($date_to) {
            $query->andWhere(['<=', 'created_at', $date_to . ' 23:59:59']);
        }

        return $query->all();
    }
}
