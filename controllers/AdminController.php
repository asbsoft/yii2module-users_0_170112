<?php

namespace asb\yii2\modules\users_0_170112\controllers;

use asb\yii2\common_2_170212\controllers\BaseAdminController;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

use Exception;

/**
 * AdminController implements the CRUD actions for User model.
 */
class AdminController extends BaseAdminController
{
    public $showRoles = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        try { // check if auth tables exists
            $authItemModel = $this->module->model('AuthItem');
            $authItemModel::find()->count();
            $authAssignmentModel = $this->module->model('AuthAssignment');
            $this->showRoles = (boolean) $authAssignmentModel::find()->count();
        } catch(Exception $ex) {}
    }
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
    {
        $searchModel = $this->module->model('UserSearch');
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
        $model = $this->module->model('User');
        $model->loadDefaultValues();
        $model->status = $model::STATUS_DELETED;
        $model->scenario = $model::SCENARIO_CREATE;
        $model->pageSize = intval($this->module->params['pageSizeAdmin']);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'page' => $model->page, 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'rolesModels' => [],
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
        if (!empty($this->module->params['pageSizeAdmin'])) {
            $model->pageSize = intval($this->module->params['pageSizeAdmin']);
        }
        $post = Yii::$app->request->post();
        if ($model->load($post) && $model->save()) {
            return $this->redirect(['index', 'page' => $model->page, 'id' => $model->id]);
        } else {
            $rolesModels = [];
            if ($this->showRoles && !$model->isNewRecord) {
                $authItemModel = $this->module->model('AuthItem');
                $allRoles = $authItemModel::find()->where(['like', 'name', 'role'])->all();
                foreach ($allRoles as $role) {
                    $data = [
                        'item_name' => $role->name,
                        'user_id'   => $model->id,
                    ];
                    
                    $authAssignmentModel = $this->module->model('AuthAssignment');
                    $next = $authAssignmentModel::find()->where($data)->one();
                    if (empty($next)) {
                        $data['value'] = false;
                        $next = $this->module->model('AuthAssignment', [$data]);
                    } else {
                        $next->value = true;
                    }
                    $rolesModels[] = $next;
                }
            }
            return $this->render('update', [
                'model' => $model,
                'rolesModels' => $rolesModels,
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
        $model = $this->findModel($id);
        $model->pageSize = intval($this->module->params['pageSizeAdmin']);
        $page = $model->calcPage();
        $result = $model->delete();
        if ($result) {
            Yii::$app->session->setFlash('success', Yii::t($this->tcModule, 'User #{id} deleted.', ['id' => $id]));
        } else {
            Yii::$app->session->setFlash('error', Yii::t($this->tcModule, 'Deletion user #{id} fail.', ['id' => $id]));
        }
        return $this->redirect(['index', 'id' => $id, 'page' => $page]);
    }

    /**
     * Change status of user.
     * @param integer $id
     * @param integer $value
     * @return mixed
     */
    public function actionChangeStatus($id, $value)
    {
        $model = $this->findModel($id);
        if (empty($model)) {
            Yii::$app->session->setFlash('error', Yii::t($this->tcModule, 'User {id} not found.', ['id' => $id]));
        } else {
            $model->status = $value;
            $model->pageSize = intval($this->module->params['pageSizeAdmin']);
            $result = $model->save(true, ['status']); // update only status field
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
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $period = isset($this->module->params['loginAdminKeepPeriodSec'])
            ? intval($this->module->params['loginAdminKeepPeriodSec'])
            : null;

        $model = $this->module->model('LoginForm');
        $model->rememberMe = false;

        if ($model->load(Yii::$app->request->post()) && $model->login($period)) {
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

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $userWithRoles = $this->module->model('UserWithRoles');
        if (($model = $userWithRoles::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
