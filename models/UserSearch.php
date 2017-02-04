<?php

namespace asb\yii2\modules\users_0_170112\models;


use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use asb\yii2\modules\users_0_170112\models\User;

/**
 * UserSearch represents the model behind the search form about `asb\yii2\modules\users_0_170112\models\User`.
 */
//class UserSearch extends User
class UserSearch extends UserWithRoles
{
    const ROLE_LOGINED = '@';

    public $roles;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'auth_key', 'password_hash', 'password_reset_token', 'email'], 'safe'],
            //[['email_confirm_token'], 'safe'],
            [['roles'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        //return Model::scenarios();
        //return User::scenarios();
        return UserWithRoles::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {

        //$query = User::find();
        $query = UserWithRoles::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
          //->andFilterWhere(['like', 'email_confirm_token', $this->email_confirm_token])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'email', $this->email]);

        // looking for roles
        if (!empty($this->roles)) {
            $query->alias('user')->leftJoin(['role' => AuthAssignment::tableName()], "user.id = role.user_id");
            if ($this->roles == self::ROLE_LOGINED) {
                $query->andWhere(['item_name' => null]);
            } else {
                $query->andFilterWhere(['like', 'item_name', $this->roles]);
            }
        }

        return $dataProvider;
    }
}
