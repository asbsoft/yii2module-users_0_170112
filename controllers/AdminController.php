<?php

namespace asb\yii2\modules\users_0_170112\controllers;

use asb\yii2\modules\users_0_170112\models\LoginForm;
use asb\yii2\modules\users_0_170112\models\User;
use asb\yii2\modules\users_0_170112\models\UserSearch;

use Yii;
use asb\yii2\controllers\BaseAdminController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * AdminController implements the CRUD actions for User model.
 */
class AdminController extends BaseAdminController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        //'actions' => ['login', 'error'],
                        'actions' => ['login'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'change-status' => ['POST'],
                    'logout' => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex($page = 1, $id = 0)
    {//echo __METHOD__;var_dump($this->module->params);
        $searchModel = new UserSearch();
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);

        $pager = $dataProvider->getPagination();
        $pager->pageSize = intval($this->module->params['pageSizeAdmin']);
        $pager->totalCount = $dataProvider->getTotalCount();

        // page number correction
        $maxPage = ceil($pager->totalCount / $pager->pageSize);
        if ($page > $maxPage) {
            $pager->page = $maxPage - 1;
        } else {
            $pager->page = $page - 1; //! from 0
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'currentId'    => $id,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();
        $model->loadDefaultValues();
        $model->status = $model::STATUS_DELETED;
        $model->scenario = $model::SCENARIO_CREATE;
        $model->pageSize = intval($this->module->params['pageSizeAdmin']);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //return $this->redirect(['view', 'id' => $model->id]);
            return $this->redirect(['index', 'page' => $model->page, 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->pageSize = intval($this->module->params['pageSizeAdmin']);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //return $this->redirect(['view', 'id' => $model->id]);
            return $this->redirect(['index', 'page' => $model->page, 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Change status of user.
     * @param integer $id
     * @param integer $value
     * @return mixed
     */
    public function actionChangeStatus($id, $value)
    {//echo __METHOD__."($id, $value)";exit;
        $model = $this->findModel($id);
        if (empty($model)) {
            Yii::$app->session->setFlash('error', Yii::t($this->tcModule, 'User {id} not found.', ['id' => $id]));
        } else {
            $model->status = $value;
            $model->pageSize = intval($this->module->params['pageSizeAdmin']);
            //$result = $model->save(); // will error if rules for another fields was changed
            $result = $model->save(true, ['status']); // update only stasus field
            if ($result) {
                Yii::$app->session->setFlash('success', Yii::t($this->tcModule, 'Status changed.'));
            } else {
                foreach ($model->errors as $attribute => $errors) {
                    Yii::$app->session->setFlash('error',
                        Yii::t($this->tcModule, "Status didn't change.")
                      . ' '
                      . Yii::t($this->tcModule, "Error on field: '{field}'.", ['field' => $attribute])
                      . ' ' . $errors[0]
                    );
                    break;
                }
            }
        }
        return $this->redirect(['index',
            'id' => $id,
            'page' => empty($model->page) ? 1 : $model->page,
        ]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        $model->rememberMe = false;

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

}
