   <?php

   use common\models\helpers\ConstantsHelper;
   use common\models\helpers\HtmlGenerator;
   use common\models\overiden\User;
   use common\models\services\UserInfo;
   use yii\helpers\Html;
   use yii\helpers\Url;
   use yii\widgets\LinkPager;

   ?>

   <div class="row table-row">
       <div class="column-12">
           <div class="table-wrapper top-user-table">
               <div class="table-inner">
                   <div class="table-head head-with-tabs head-custom-tabs">
                       <div class="tbl-icon tbl-icon-select">
                           <img src="/images/champ.svg" alt="">
                       </div>
                       <div class="left-head-text">
                           <div class="title-w-select"> РЕЙТИНГ ПОЛЬЗОВАТЕЛЕЙ 2</div>


                           
                       </div>

                           <div class="right-head-tab" id="tope100">
                               <div class="for-mobile-drop">
                                   <a href="#" class="trig-filter">За месяц</a>
                                   <ul class="head-tabs">
                                       <li class="<?=$periodOne?>">
                                           <a href="<?=Url::to(['/bet']);?>">За месяц</a>
                                       </li>
                                       <li class="<?=$period3m?>">
                                           <a href="<?=Url::to(['/bet','period'=>ConstantsHelper::PERIOD_3_M]);?>">3 месяца</a>
                                       </li>
                                       <li class="<?=$periodAll?>">
                                           <a href="<?=Url::to(['/bet','period'=>ConstantsHelper::PERIOD_ALL]);?>">За все время</a>
                                       </li>
                                   </ul>
                               </div>
                           </div>

                   </div>

                   <div class="table-body">
                       <div class="table-block-rating">
                           <div class="table-block-rating-inner">
                               <div class="hr table-head">
                                   <div class="td table-cell td-count">#</div>
                                   <div class="td table-cell td-user"></div>
                                   <div class="td table-cell td-profit">Profit</div>
                                   <div class="td table-cell td-passability">Проходимость</div>
                                   <div class="td table-cell td-coeficient">Коэффициент</div>
                                   <div class="td table-cell td-roi">ROI</div>
                                   <div class="td table-cell td-roi">Плюс</div>
                               </div>

                               <?php
                               $models = array_values($dataProvider->getModels());
                               $keys = $dataProvider->getKeys();
                               $rows = [];
                               foreach ($models as $index => $model) {
                                   $user=User::find()->where(['id'=>$model['user_id']])->one();
                                   $useeInfo=new UserInfo($user->id);
                                   $pathToUser=Url::toRoute(['/account/view','id'=>$user->id]);
                                 $profite=  sprintf("%01.2f %%", $model['sume']);
                                 $penet=  sprintf("%01.2f %%", $model['penet']);
                                 $coef=sprintf("%01.2f", $model['mdc']);
                                   $roi=sprintf("%01.2f", $model['ro']);
                                   echo '         <div class="hr table-row">
                                   <div class="td table-cell td-count">'.($index+1).'</div>
                                   <div class="td table-cell td-user">
                                      <div class="row-ava">
                                           <div class="rate-avatar">
                                             <a href="'.$pathToUser.'">  <div class="circle-wrapper" data-ptc="'.$useeInfo->getUserLevelNumber().'">
                                                   <div class="circle"></div>
                                               </div>
                                               </a>
                                               <div class="avatar-user">
                                                   <img src="/'.$user->imageurl.'" alt="'.$useeInfo->getUserName().'">
                                               </div>
                                               
                                           </div>
                                           <div class="user-info">
                                               <a href="'.$pathToUser.'" style="color: white;"> <h4 class="name-r">'.$useeInfo->getUserName().'</h4> </a>
                                           </div>
                                       </div>
                                   </div>
                                   <div class="td table-cell td-profit">'.$profite.'</div>
                                   <div class="td table-cell td-passability">'.$penet.'</div>
                                   <div class="td table-cell td-coeficient">'.$coef.'</div>
                                   <div class="td table-cell td-roi">'.$roi.'</div>
                                    <div class="td table-cell td-roi">'.$roi.'</div>
                               </div>';
                               }

                               ?>




                           </div>
                       </div>
                   </div>


                   <?php

                   $pagination = $dataProvider->getPagination();
                   if ($pagination === false || $dataProvider->getCount() <= 0) {

                   }

                   ?>
                   <div class="table-footer">
                     <div class="pagination">
                         <?php
                         // отображаем ссылки на страницы
                         echo LinkPager::widget([
                             'pagination' => $pagination,
                             'firstPageCssClass' => 'first-pag',
                             'lastPageCssClass' => 'last-pag',
                              'options' => ['class' => 'pagination-list'],
                         ]);
                         ?>
                     </div>


                   </div>

               </div>
           </div>
       </div>
       <div class="column-12 block-bnr">
           <a href="#">
               <img src="/images/ad@3x.jpg" alt="">
           </a>
       </div>
   </div>