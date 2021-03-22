# Symfony

## In the terminal

```console
Composer -v
symfony check: requirements
```

## CREATE A NEW SYMFONY PROJECT

Crea un nuevo proyecto symfony, es pesado y se tarda un poco en descargar, sobre todo en windows

```console
symfony new test --full
```

When the project is ready, go into the project folder and start the server

```console
cd test
symfony server:start
```

Click or copy the server address to open it in the browser

Abrir una ventana VSCode directo en el folder test

```console
code .
symfony console list
```

## Crear nuevo controller

```console
symfony console make:controller TestController
```

use - para importar un bundle

## Crear una entity

```console
symfony console make:entity Test
```

## To create a database

### Create with doctrine migrate

In the .env file uncomment the database engine you want to use and enter
the credentials and the dbname

```console
# DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
DATABASE_URL="mysql://user:pw@127.0.0.1:3306/dbname?serverVersion=5.7"
# DATABASE_URL="postgresql://db_user:db_password@127.0.0.1:5432/db_name?serverVersion=13&charset=utf8"
```

After that, create the database

```console
symfony console doctrine:database:create
symfony console make:migration
```

The object of the migration is to make our entity persist in the database

Crea una nueva tabla por cada clase, con una función UP y una DOWN.
La función DOWN sirve para regresar al estado anterior a la migración.

Persistence - save it in the database
Lifecycle - para mostrarlo en la página web

```console
symfony console doctrine:migrations:migrate
```

Para ver las opciones de doctrine:migrations

```console
symfony console doctrine:migrations:help
```

### Create with doctrine schema

```console
symfony console doctrine:database:create
symfony console doctrine:schema:update --force
```

We use _force_ because...

We use schema cuando partimos de cero o cuando no queremos conservar los datos que ya están en la base de datos. Cuando queremos rehacer la estructura de la BD. Con este método no hay log que nos dice lo que se hizo.

Para cuando queremos trabajar con una BD que ya existe y tiene datos, usamos migrations. Migrations si tiene un log. El log está en la tabla migrations.

## To add properties to a class

```console
symfony console make:entity Post
```

If you make a mistake, it´s best to delete it in the class, with its getter and setter and then add it again with make:entity over the class

## Bundle security

```console
symfony console make:user
```

After creating the user, you have the file /config/packages/security.yaml which contains all the security configuration, including the encription algorithm.

## To add a foreign key between tables

I add a new field in my base table, in the example it´s Post and in the Field Type I put relation (to get the wizard that gives me the relationship options). Indicate the class that the entity should be related to and then follow all the prompts.

## Open a local server browser window from the console

```console
symfony open:local
```

## Bundle que permite crear datos ficticios para mi BD

```console
symfony composer require orm-fixtures --dev
```

orm-fixtures es un bundle de Doctrine

```console
symfony console make:fixtures PostFixtures
```

After, a new DataFixtures folder will be created.

### Update the PostFixtures file

Manually include the following namespace into PostFixtures:
use App\Entity\Post;

Make the constructor of the Security

Install the extension **PHP Namespace Resolver**

Code inside PostFixtures:

```php
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function load(ObjectManager $manager)
    {
        $user = $this->security->getUser();
        $post1 = new Post();
        $post1->setTitle('My first post')
            ->setContent('lorem ipsum dolor sit amet, consectetur')
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setAuthor($user);

        //Request our manager to persist my post in the DB, insert into
        $manager->persist($post1);


        $manager->flush();
```

To run and create the data with fixtures

```console
symfony console doctrine:fixtures:load --append
```

# Since PostFixtures didn´t work, we´re going to do UserFixtures

symfony console make:fixtures UserFixtures

## Create an authentication

```console
symfony console make:auth
```

After creating an authenticator, a Security folder is created with a UserAuthenticator.php file.
We have to customize this file.
Finish the redirect TODO in the onAuthenticationSuccess() method, uncommenting and changing the following line:

```console
return new RedirectResponse($this->urlGenerator->generate('app-home'));
```

At the end, review and adapt the login template and the SecurityController file.

This creates an automatic login page _localhost/login_
In the security controller, with no changes made we have this:

```php
/**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
```

## Create user registration forms

This is to automatically create forms to add users to our database - create the persistance of our data, make an insert into the DB.

```console
symfony console make:registration-form
```

# ENV file

Setting our application environment:

```php
APP_ENV=dev
```

Add the connection parameters to our database using DATABASE_URL

```php
DATABASE_URL="mysql://root:root@127.0.0.1:3306/symfonyblog2021?serverVersion=5.7"
```

## View log

```console
symfony console debug:autowiring
```

## Install Bootstrap

BE CAREFUL! Do not use yarn and npm since it may cause conflicts and casser everything. Stick to one package handler.

Note: _Do this from cmd, not from the terminal inside vscode._

```console
symfony composer require symfony/webpack-encore-bundle
npm i bootstrap --dev
npm install jquery popper.js --dev
```

With yarn

```console
yarn add bootstrap popper.js jquery
```

En app.css add the following line at the top

```php
@import "~bootstrap/scss/bootstrap";
```

In config - twig.yaml add: - this is not necessary

```php
form_themes: ["bootstrap_4_layout_html.twig"]
```

In the terminal

```console
symfony run npm encore dev
```

```console
npm i node-sass sass-loader --dev
```

En app.js cambiar el .css por .scss
import "./styles/app.scss";

En webpack.config.js decomentar la linea 59 y cambiar al extención de app.css a app.scss

APP.js

```js
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import "./styles/app.scss";
import "bootstrap";

// const $ = require("jquery");
// require("bootstrap");

// start the Stimulus application
import "./bootstrap";
```

npm encore does not work so we install yarn

```console
npm i yarn --global
```

La siguiente línea permite compilar y recompilar cada vez que hacemos una modificación en CSS

```console
symfony run -d yarn encore dev --watch
```

Al final modificar el archivo template Base Twig para decomentar las líneas entre stylesheets y javascripts

```php
        {% block stylesheets %}
            {{ encore_entry_link_tags('app') }}
        {% endblock %}

        {% block javascripts %}
            {{ encore_entry_script_tags('app') }}
        {% endblock %}
```

## Common templates like header and footer

Create a subfolder inside templates named _common_, and inside it put all templates that are common, such as header, footer, nav

## Reference to a new page, different than index in the controller

Create the page in the template of the class and change the render target in the return in the controller. The snippet below is from RandomController.php

```php
public function random(): Response
    {
        $number = rand(0, 100);
        //dd($number); //DD means dump and die, it kills the code after dumping the variable

        return $this->render('random/random.html.twig', [
            'number' => $number
        ]);
}
```

## To pass variables via url in twig template

Use the path function in twig to use the name of the route to pass the path and then send the variables you want to send. To add more variables, separate them with commas.

```php
<a href="{{ path('app_snowflake_id', {'id': snowflake.id}) }}"> {{ snowflake.name }} </a>
```

You can just put the variables you send in the parameters of the function of your route in the controller. In the example, the parameter is $id

```php
 /**
     * @Route("/snowflake/{id}", name="app_snowflake_id")
     */
    public function details(SnowflakeRepository $snowflakeRepository, $id): Response
    {

        //dd($request->query->get('id'));
        $snowflake = $snowflakeRepository->findOneById($id); //(['id' => $id]);


        return $this->render('snowflake/details.html.twig', ['snowflake' => $snowflake]);
    }
```

## Select queries to the DB

Symfony makes available already constructed methods to select data from the database, through the EntityRepository file. These methods include findAll(), findOneby(), find(), etc.
We can use these methods in the controller by adding the repository in the parameters of our controller function.

```php
/**
     * @Route("/snowflake", name="app_snowflake")
     */
    public function home(SnowflakeRepository $snowflakeRepository): Response
    {
        $snowflakes = $snowflakeRepository->findAll();
        //dd($snowflakes);

        return $this->render('snowflake/index.html.twig', ['snowflakes' => $snowflakes]);
    }
```

A controller is a function that gets a request and produces a response to this request via the return.

We use render to open a template and we can send them an array with the data we have.

This is another option for getting the details, synfony automatically recognizes the {id} on the route and you can just set the object as a parameter in the function. This works when you are using the ID of the object or you can also use a slug **(research more about this)**.

It is important to include the requirements to specify that the ID is a number (we do this with a regex).
If we don´t do this anything that goes in the {id} position in the route is going to be consider an ID and call the function. That is why my new form was not working and redirected always to the details page

```php
 /**
     * @Route("/snowflake/{id}", name="app_snowflake_id",requirements={"id"="\d+"})
     */
    public function details2(Snowflake $snowflake): Response
    {

        return $this->render('snowflake/details.html.twig', ['snowflake' => $snowflake]);
    }
```

## Forms

To view the info of my routes:

```console
symfony console debug:route
```

To create a new form for my Entity:
Pending update

---

Request is the request manager and gives me access to my requests

The EntityManagerInterface is an object manager because we are going to add things to our DB, objects that are gong to persist.

We need to use the handleRequest method to indicate that we want to use the request. _RESEARCH MORE_

- <https://symfony.com/doc/current/reference/forms/types.html>

```php
/**
     * @Route("/snowflake/new", name="app_snowflake_new", methods="GET|POST")
     */
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $snowflake = new Snowflake();
        $form = $this->createForm(SnowflakeType::class, $snowflake);

        $form->handleRequest($request);

        //The manager here inserts the new data if conditions are met in the form
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($snowflake);
            $manager->flush();

            //Al final del ciclo de vida de la página y haber hecho todo lo anterior, redireccionamos
            return $this->redirect('/snowflake');
        }

        return $this->render('snowflake/create.html.twig', ['form' => $form->createView()]);
    }
```

### Get current datetime automatically when creating a snowflake

#### Method 1

For our form to get the current datetime, you have to modify the setCreatedAt() in the Snowflake entity but before that, above the entitiy class declaration include the line @ORM\HasLifecycleCallbacks()

```php
<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SnowflakeRepository;

/**
 * @ORM\Entity(repositoryClass=SnowflakeRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Snowflake
```

After that, just apres setCreatedAt we have to create a new function. _PrePersist_ le dice que justo antes de la persistencia del objeto, le asigne la datetime del momento.

```php
 //Just apres setCreatedAt we have to create a new function
    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime('now');
    }
```

#### Method 2

Do it in the controller with the method setCreatedAt before you persist the new snowflake

```php
if ($form->isSubmitted() && $form->isValid()) {
            $snowflake->setCreatedAt(new \DateTime('now'));
            $manager->persist($snowflake);
            $manager->flush();

            //Al final del ciclo de vida de la página y haber hecho todo lo anterior, redireccionamos
            return $this->redirect('/snowflake');
        }
```

### Form verifications

- These are done in the controller, to make sure we are inputing the right fields.
- htmlspecialchars is done automatically by symfony.
- Attaching images in a form is handled by a special bundle
- {{ dangerous|raw }} -- research about raw - {# the dangerous line is just for an example. This is dangereous because it exposes us to the xss attack #} For an example of the xss atack go to Axel´s github xss-quick repository.
- spoofing de cookie

### Fragmentation de Template

Create in the common folder a file \_form.html.twig and then we include it in the create.html.twig file passing a parameter for the submit button.

### Includes

When we just declare a route in the include we can use the {%%}, it´s just a declaration de récupération d'un fichier

```php
{% include './common/navbar.html.twig' %}
```

If we use include as a function to pass a parameter, we use {{}}

```php
{{ include ('./common/_form.html.twig',{submitButtonText:'Create a snowflake'}) }}
```

### Delete

Passing the id to delete via the get method is very unsafe, it´s better to create a form with hidden fields.

csrf_token es diferente para cada botón delete.

## Symfony access control

Sur security.yaml
We can include the permits in the documentation via @IsGranted() OR @Security()

```php
 /**
     * @Route("/snowflake/delete/{id<\d+>}",name="app_snowflake_delete", methods="DELETE")
     * @IsGranted("ROLE_ADMIN, OWN_USER")
     * @Security("has_role('ROLE_ADMIN')")
     */
```

Dans twig to show/hide elements based on the role of the user

```php
{% if app.user is same as(snowflake.author) %}
```

## Troubleshooting Webpack

Bootstrap stopped working and the webpack.config.js file is showing an error.

Hay que reinstalar encore usando yarn:

```console
yarn encore dev
```

Si yarn no funciona porque no encuentra el command encore, hacer un update de composer, as follows. Al terminar, correr _yarn encore dev_.

```console
symfony composer update
```

# Links and things to research

- _LOOKUP namespace, npm, nodejs, nvm, chocolatery_
- webpage lifecycle: existance, ...

- Cambiar los primary, secondary, warning, etc colors de bootstrap
- Using a hidden field to delete a record from a DB to make sure that the person who deleted really wanted to delete the record
- _emet_

- removebg.com
- free-for-dev.com
- Hosting for Symfony projects: eroku
- faker.php
- jamstack
- snowpack
- <https://stackoverflow.com/questions/43832163/how-to-send-params-in-url-query-string-in-symfony/43832271>
- <https://codereviewvideos.com/course/symfony-basics/video/how-to-get-the-request-query-parameters-in-symfony>
- Forms:
  - <https://www.kaherecode.com/tutorial/creer-un-blog-avec-symfony-les-formulaires>
  - <https://nouvelle-techno.fr/actualites/symfony-4-creer-un-blog-pas-a-pas-utiliser-les-formulaires>
- unshorten.it - para verificar links
- hacksplaning.com - cross-site request forgery CSRF - exercises
- Frontendmentor.io
- exercism.io
- codewars.com
