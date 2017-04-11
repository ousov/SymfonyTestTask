<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Users;
use AppBundle\Entity\User_profile;
use AppBundle\Entity\User_avatar;
use AppBundle\Entity\User_phones;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MyController extends Controller
{

    //Функция по просмотру полного профиля user
    public function detailsAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:Users')->getUserFullInfoById($id);
        $user = $em->getRepository('AppBundle:Users')->reNameUserAvatarLink($user);
        return $this->render('AppBundle:Pages:details.html.twig', array(
            'user' => $user,
        ));
    }

    //Функция по удалению пользователя
    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:Users')->find($id);
        //Более правильно сделать запрос на репозиторий и сделать команду мультиудаления не каскадируемых таблиц
        //DELETE FROM users, user_profile, user_avatar, user_phones USING users INNER JOIN user_profile INNER JOIN user_avatar INNER JOIN user_phones WHERE users.id=user_profile.user_id AND user_profile.avatar_id=user_avatar.id AND user_profile.phone_id=user_phones.id AND users.id=$id
        //Тем самым обеспечив удаление связанных сущностей без каскада
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('homepage');
    }

    //Функиця индексной страницы с выводом всех пользователей и возможностью их детализации
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AppBundle:Users')->getAllUsers();
        //Перебор массива и разделение на внутренние ссылки к фото и загруженные вручную
        //Переопределение ссылки на фото
        $users = $em->getRepository('AppBundle:Users')->reNameUserAvatarLink($users);
        //Paginator
        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 5)
        );
        return $this->render('AppBundle:Pages:index.html.twig', array(
            'users' => $result,
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response;
     * Создание нового пользователя
     */
    public function createAction(Request $request)
    {
        $user = new Users();
        $avatar = new User_avatar();
        $phone = new User_phones();
        $user_prof = new User_profile();
        $form_user = $this->createFormBuilder()
            ->add('name', TextType::class)
            ->add('login', TextType::class)
            ->add('email', EmailType::class)
            ->add('passwordHash', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options' => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ))
            ->add('bio', TextareaType::class, array(
                'attr' => array(
                    'class' => 'ckeditor',
                ),
            ))
            ->add('address', TextType::class)
            ->add('City', ChoiceType::class, array(
                'choices' => array(
                    'Днепр' => 'Днепр',
                    'Киев' => 'Киев',
                    'Одесса' => 'Одесса',
                ),
            ))
            ->add('Country', ChoiceType::class, array(
                'choices' => array(
                    'Украина' => 'Украина',
                    'Беларусия' => 'Беларусия',
                ),
            ))
            ->add('avatar', TextType::class, array(
                'required' => false,
            ))
            ->add('avatar2', FileType::class, array(
                'label' => 'Загрузите Фото',
                'required' => false,
            ))
            ->add('phone', TextType::class, array(
                'attr' => array(
                    'id' => 'inputTel',
                ),
            ))
            ->add('save', SubmitType::class, array('label' => 'Create User'))
            ->getForm();
        //Проверка возврата Request и подтверждения форм
        //Спасибо PHPStorm, который не находит данные методы в проекте (, но они есть и оно работает согласно докам
        $form_user->handleRequest($request);
        if ($form_user->isSubmitted() && $form_user->isValid()) {
            $datas = $form_user->getData();
            //Проверка на ввод имени
            $i = preg_match('/^[A-Za-z0-9]+$/', $datas['name']);
            if ($i !== 1) {
                return $this->render('AppBundle:Pages:create.html.twig', array(
                    'message' => 'В имени может содержаться только кирилица',
                    'form' => $form_user->createView(),
                ));
            }
            //Если форма валидна заносим основные данные
            $em = $this->getDoctrine()->getManager();
            if ($datas['avatar'] == null && $datas['avatar2'] == null) {
                $datas['avatar'] = 'Avatar.jpg';
            }
            //Получаем и фильтруем файл :)
            if ($datas['avatar2'] != null) {
                $file = $datas['avatar2'];
                $fileExtension = $file->guessExtension();
                $types = array("jpeg", "png", "jpg");
                if (in_array($fileExtension, $types)) {
                    $fileName = md5(uniqid()) . '.' . $fileExtension;
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                    $avatar->setLinkAvatar($fileName);
                } else {
                    return $this->render('AppBundle:Pages:create.html.twig', array(
                        'form' => $form_user->createView(),
                        'message' => 'Для изображения Аватара можно использовать только jpeg, jpg или png файлы',
                    ));
                }
            } else {
                if ($datas['avatar'] != null) {
                    $avatar->setLinkAvatar($datas['avatar']);
                }
            }
            $phone->setPhoneNumber($datas['phone']);
            $user->setEmail($datas['email']);
            $user->setName($datas['name']);
            $user->setLogin($datas['login']);
            $user->setPasswordHash($datas['passwordHash']);
            //Теперь заносим данные в связанную таблицу
            $user_prof->setAddress($datas['address']);
            $user_prof->setBio($datas['bio']);
            $user_prof->setCity($datas['City']);
            $user_prof->setCountry($datas['Country']);
            $user_prof->setUsers($user);
            $user_prof->setUserAvatar($avatar);
            $user_prof->setUserPhone($phone);
            $em->persist($avatar);
            $em->persist($phone);
            $em->persist($user);
            $em->persist($user_prof);
            $em->flush();
            return $this->redirectToRoute('homepage');
        }
        //Первичный запуск вывода формы
        return $this->render('AppBundle:Pages:create.html.twig', array(
            'form' => $form_user->createView(),
        ));
    }

    //Функция по редактированию пользователя
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userToChange = $em->getRepository('AppBundle:Users')->getUserFullInfoById($id);
        //Запоминаем в отдельную переменную введенное имя на аватар пользователем
        $savedValueAvatar = $userToChange[0]['linkAvatar'];
        //Перелинковываем ссылки на изображения для отображения на странице
        $userToChange = $em->getRepository('AppBundle:Users')->reNameUserAvatarLink($userToChange);
        //Получаем id для моделей
        $user_profID = $em->getRepository('AppBundle:User_profile')->findProfById($id);
        $user_avatarID = $em->getRepository('AppBundle:User_avatar')->findAvatarById($id);
        $user_phoneID = $em->getRepository('AppBundle:User_phones')->findPhoneById($id);
        //Получаем модели для изменения
        $user = $em->getRepository('AppBundle:Users')->find($id);
        $user_prof = $em->getRepository('AppBundle:User_profile')->find($user_profID[0]['id']);
        $user_avatar = $em->getRepository('AppBundle:User_avatar')->find($user_avatarID[0]['id']);
        $user_phone = $em->getRepository('AppBundle:User_phones')->find($user_phoneID[0]['id']);
        //Начало создания формы для редактирования
        $form_user = $this->createFormBuilder()
            ->add('name', TextType::class)
            ->add('login', TextType::class)
            ->add('email', EmailType::class)
            ->add('passwordHash', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options' => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ))
            ->add('bio', TextareaType::class, array(
                'attr' => array(
                    'class' => 'ckeditor',
                ),
            ))
            ->add('address', TextType::class)
            ->add('City', ChoiceType::class, array(
                'choices' => array(
                    'Днепр' => 'Днепр',
                    'Киев' => 'Киев',
                    'Одесса' => 'Одесса',
                ),
            ))
            ->add('Country', ChoiceType::class, array(
                'choices' => array(
                    'Украина' => 'Украина',
                    'Беларусия' => 'Беларусия',
                ),
            ))
            ->add('avatar', TextType::class, array(
                'required' => false,
            ))
            ->add('avatar2', FileType::class, array(
                'label' => 'Загрузите Фото',
                'required' => false,
            ))
            ->add('phone', TextType::class, array(
                'attr' => array(
                    'id' => 'inputTel',
                ),
            ))
            ->add('save', SubmitType::class, array('label' => 'Update User'))
            ->getForm();
        //Конец создания формы для редактирования
        //Проверка на валидность
        $form_user->handleRequest($request);
        if ($form_user->isSubmitted() && $form_user->isValid()) {
            $datas = $form_user->getData();
            //Проверка на ввод имени
            $i = preg_match('/^[A-Za-z0-9]+$/', $datas['name']);
            if ($i !== 1) {
                return $this->render('AppBundle:Pages:edit.html.twig', array(
                    'message' => 'В имени может содержаться только кирилица',
                    'form' => $form_user->createView(),
                    'user' => $userToChange,
                ));
            }
            //Если форма валидна заносим основные данные
            $em = $this->getDoctrine()->getManager();
            //Если пользователь не поменял картинку на аватаре - сохраняем ранее введенную
            if ($datas['avatar'] == null && $datas['avatar2'] == null) {
                $datas['avatar'] = $savedValueAvatar;
            }
            //Получаем и фильтруем файл :)
            if ($datas['avatar2'] != null) {
                $file = $datas['avatar2'];
                $fileExtension = $file->guessExtension();
                $types = array("jpeg", "png", "jpg");
                if (in_array($fileExtension, $types)) {
                    $fileName = md5(uniqid()) . '.' . $fileExtension;
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                    $user_avatar->setLinkAvatar($fileName);
                } else {
                    return $this->render('AppBundle:Pages:edit.html.twig', array(
                        'user' => $userToChange,
                        'form' => $form_user->createView(),
                        'message' => 'Для изображения Аватара можно использовать только jpeg, jpg или png файлы',
                    ));
                }
            } else {
                if ($datas['avatar'] != null) {
                    $user_avatar->setLinkAvatar($datas['avatar']);
                }
            }
            $user_phone->setPhoneNumber($datas['phone']);
            $user->setEmail($datas['email']);
            $user->setName($datas['name']);
            $user->setLogin($datas['login']);
            $user->setPasswordHash($datas['passwordHash']);
            //Теперь заносим данные в связанную таблицу
            $user_prof->setAddress($datas['address']);
            $user_prof->setBio($datas['bio']);
            $user_prof->setCity($datas['City']);
            $user_prof->setCountry($datas['Country']);
            $em->flush();
            return $this->redirectToRoute('homepage');
        }
        //Это идет на вывод
        return $this->render('AppBundle:Pages:edit.html.twig', array(
            'user' => $userToChange,
            'form' => $form_user->createView(),
        ));
    }
}