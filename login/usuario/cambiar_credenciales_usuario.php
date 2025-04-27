<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cambiar credenciales</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />
    <?php
        error_reporting( E_ALL );
        ini_set("display_errors", 1 );    

        require('../../util/conexion.php');
        require('../../util/funciones/utilidades.php');

        define('IMG_USUARIO','/img/usuario/');

        session_start();
        if (!isset($_SESSION["usuario"])) { 
            header("location: ../usuario/iniciar_sesion.php");
            exit;
        }

        $id_usuario = $_SESSION['usuario'];

        $sql = $_conexion->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
        $sql->bind_param("i", $id_usuario);
        $sql->execute();
        $resultado = $sql->get_result();
    ?>
    <style>
      .error {
        color: red;
      }

      body {
        margin-top: 20px;
        background: #f5f5f5;
      }
      /**
        * Panels
        */
      /*** General styles ***/
      .panel {
        box-shadow: none;
      }
      .panel-heading {
        border-bottom: 0;
      }
      .panel-title {
        font-size: 17px;
      }
      .panel-title > small {
        font-size: 0.75em;
        color: #999999;
      }
      .panel-body *:first-child {
        margin-top: 0;
      }
      .panel-footer {
        border-top: 0;
      }

      .panel-default > .panel-heading {
        color: #333333;
        background-color: transparent;
        border-color: rgba(0, 0, 0, 0.07);
      }

      form label {
        color: #999999;
        font-weight: 400;
      }

      .form-horizontal .form-group {
        margin-left: -15px;
        margin-right: -15px;
      }
      @media (min-width: 768px) {
        .form-horizontal .control-label {
          text-align: right;
          margin-bottom: 0;
          padding-top: 7px;
        }
      }

      .profile__contact-info-icon {
        float: left;
        font-size: 18px;
        color: #999999;
      }
      .profile__contact-info-body {
        overflow: hidden;
        padding-left: 20px;
        color: #999999;
      }
      .profile-avatar {
        width: 200px;
        position: relative;
        margin: 0px auto;
        margin-top: 196px;
        border: 4px solid #f3f3f3;
      }
    </style>
  </head>
  <body>
    <link
      href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css"
      rel="stylesheet"
    />
    <div class="container bootstrap snippets bootdeys">
      <div class="row">
        <div class="col-xs-12 col-sm-9">
          <form class="form-horizontal">
            <div class="panel panel-default">
              <div class="panel-body text-center">
                <?php while ($fila = $resultado->fetch_assoc()) { ?>
                    <img src="<?php echo IMG_USUARIO.$fila['foto_usuario']?>" alt="Foto de perfil" width="32" height="190" class="rounded-circle profile-avatar"/>
                <?php } ?>
              </div>
            </div>
            
            <div class="panel panel-default">
              
              <div class="panel-body">
                
                
                <div class="form-group">
                  <label class="col-sm-2 control-label">Nuevo email</label>
                  <div class="col-sm-10">
                    <input type="email" class="form-control" />
                  </div>
                </div>
              </div>
            </div>

            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">Security</h4>
              </div>
              <div class="panel-body">
                <div class="form-group">
                  <label class="col-sm-2 control-label">Current password</label>
                  <div class="col-sm-10">
                    <input type="password" class="form-control" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label">New password</label>
                  <div class="col-sm-10">
                    <input type="password" class="form-control" />
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-10 col-sm-offset-2">
                    <div class="checkbox">
                      <input type="checkbox" id="checkbox_1" />
                      <label for="checkbox_1">Make this account public</label>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-10 col-sm-offset-2">
                    <button type="submit" class="btn btn-primary">
                      Submit
                    </button>
                    <button type="reset" class="btn btn-default">Cancel</button>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- <div class="container">
        <h1>Cambiar credenciales</h1>
        <?php
        $usuario = $_GET["usuario"];
        $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario'";
        $resultado = $_conexion -> query($sql);
        
        while($fila = $resultado -> fetch_assoc()) {
            $contrasena = $fila["contrasena"];
        }

        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $nueva_contrasena = $_POST["nueva_contrasena"];

            if($nueva_contrasena == ""){
                $err_contrasena = "La contraseña es obligatoria";
            } else {
                if(strlen($nueva_contrasena) > 15 || strlen($nueva_contrasena) < 8){
                    $err_contrasena = "La contraseña tiene que tener como minimo 8 y maximo 15 caracteres";
                } else {
                    $patron = "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/";
                    if(!preg_match($patron, $nueva_contrasena)){
                        $err_contrasena = "La contraseña tiene que tener letras en mayus y minus, algun numero y puede tener caracteres especiales";
                    } else {
                        $contrasena_cifrada = password_hash($nueva_contrasena,PASSWORD_DEFAULT);
                        // Modifica la contraseña
                        $sql = "UPDATE usuarios SET contrasena = '$contrasena_cifrada' WHERE usuario = '$usuario'";
                        $_conexion -> query($sql);
                    }                    
                }
            }
            
        }
        ?>
        <form class="col-6" action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input class="form-control" type="text" name="usuario" value="<?php echo $usuario ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input class="form-control" type="password" name="nueva_contrasena">
                <?php if(isset($err_contrasena)) echo "<span class='error'>$err_contrasena</span>"; ?>
            </div>
            <div class="mb-3">
                <input type="hidden" name="usuario" value="<?php echo $usuario ?>">
                <input class="btn btn-primary" type="submit" value="Confirmar">
                <a href="../index.php" class="btn btn-outline-secondary">Volver a inicio</a>
            </div>
        </form>
    </div> -->

    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
  </body>
</html>
