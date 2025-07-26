<h2>Instrucciones para empezar un proyecto LARAVEL en Taller Empresarial</h2>
<p>1 npm install</p>
<p>2 composer install</p>
<p>crea tu .env desde env.example con los siguientes cambios</p>
<code>
APP_NAME=TE2.0


DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=****NOMBREDBAPLICACION***
DB_USERNAME=root
DB_PASSWORD=



MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=teasd3812@gmail.com
MAIL_PASSWORD="iksr qwtv ifci kkjf"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=teasd3812@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

IPS_PERMITIDAS_EN_MANTENIMIENTO=127.0.0.1,80.102.160.22

<p>php artisan key:generate</p>

## VARIABLES AÑADIDAS POR TE 2.0

</code>

<p>npm run build</p>
<p>npm run build-rtl</p>
<p>crea la bd</p>
<p>php artisan migrate --seed</p>
<p>php artisan serve</p>

<p>ACCESO A LA APP</p>
<p>desarrollo@tallerempresarial.es</p>
<p>iniciotaller</p>

<h3>PERSONALIZACION</h3>
<p>Esta aplicacion base esta creada con un diseño neutro que puede valer para cualquier cliente, pero si se necesita personalizar el logo imagen de fondo del login o los colores PRIMARY  o del SIDEBAR (con esto seria suficiente para personalizar)</p>
<p>No personalizar nada dentro del public ya que se sobreescribira al hacer el npm run build</p>
<p>Personalizar siempre desde "resources" en "imagenes" o en "scss" los estilos sobrescribiondo o modificando en _variables (para estos estilos principales <b>buscar //TE2.0</b> que son los más importantes)</p>
<p>Los js tambien desde "resources" si no se machacaran</p>

