<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel= 'stylesheet' type='text/css' href="styles/biblio.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
  <title>Bibl.io</title>
</head>
<body>
  <header>
    <a href="index" class="logo"><img width="70px" height="48.125px" src="Images/biblio.png"><h1 class="logotext">Biblio</h1></a>
    <nav>
      <?php
      if(isset($_SESSION['logged_in']) and $_SESSION['logged_in']){
        echo "<h1 style='color: white'><span ".((isset($_SESSION['admin']) and $_SESSION['admin'])?'style="color: #FFDA1E"':null).">".$_SESSION['nickname']."</span> logado</h1>
        <a href='index'><h1>Home</h1></a>";
        if(isset($_SESSION['admin']) and $_SESSION['admin']){
          echo 
            "<a href='books'><h1>Livros</h1></a>
            <a href='emprest'><h1>Empréstimos</h1></a>";
        }
        echo "<a href='logout'><h1>Logout</h1></a>";
      }
      else{
      echo "<a href='login'><h1>Login</h1></a>
            <a href='cadastro'><h1>Cadastro</h1></a>";
      }
      ?>
    </nav>
  </header>
  <main>
    <?php
      if(session()->getFlashdata('item')){
        echo "<div class='alert-success'><h1>".session()->getFlashdata('item')."</h1></div>";
      }
    ?>
    <?php if(isset($bodycontent))echo $bodycontent ?>
  </main>
  <footer>
    <div class="nav admin-controls"></div>
    <div class="credits">Desenvolvido por M&M Development - 2024 ©</div>
  </footer>
</body>
</html>