<?php

namespace App\Controllers;

use App\Models\BiblioModel;
use App\Models\BiblioBooks;
use App\Models\BiblioRelations;

class BiblioController extends BaseController
{
    public function checkLogin(){
      if(!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']){
        return false;
      }
      return true;
    }   

    public function isAdmin($userid){
      $myModel = new BiblioModel();
      $user = $myModel->find($userid);
      if($user['adm'] == 'YES') return true;
      return false;
    }

    public function index()
    {   
      if(!$this->checkLogin()) return redirect()->to('login');

      $this->throwBooksToSession();
      $userBooks = $this->session->get('booksRelated');
      $bodycontent = "
      <h1 class='listHeader'>Sua Biblioteca</h1>
          <div class='gridContainer' style='margin-bottom: 20px;'>";
      if(isset($userBooks) && !empty($userBooks)){
        foreach($userBooks as $book){
          extract($book);
          $bodycontent.="<div class='gridItem'>
                  <div class='bookImage'>
                
                  </div>
                  <div class='bookLabelContainer'>
                    <p class='bookLabel'>Título: ".$title."</p>
                    <p class='bookAuthor'>Autor: ".$author."</p>
                    <p class='bookEditor'>Editora: ".$editor."</p>
                    <p class='bookYear'>Publicado em: ".$year."</p>
                  </div>
                  <div class='bookOptions'>
                    <button class='bookOption viewOption' disabled>Visualizar <i class='fa fa-file'></i></button>
                    <form action='givebackbook' method='post'>
                      <input type='hidden' name='id_liv' value='".$id."'>
                      <button class='bookOption givebackOption'>Devolver <i class='fa fa-rotate-left'></i></button>
                    </form>
                  </div>
                  </form>
                </div>";
        }
      }

      $bodycontent .= "</div>
      <div class='listHeader' id='booksList'>
      <h1>Nossos Livros!</h1>
      <div class='box-search'>
        <input class='search-input' type='search' name='search' id='search' placeholder='Procure por um livro:'>
        <button onclick='searchData()' class='search-button'><i class='fa fa-search'></i></button>
      </div>
      <script>
        var search = document.getElementById('search');
        search.addEventListener('keydown', function(e) {if(event.key === 'Enter') searchData()}) 
        function searchData(){
          window.location = 'index?search='+search.value+'#booksList'
        }
      </script>
      </div>
      </div>
      <div class='gridContainer'>";
      $searchdata = '';
      if(isset($_GET['search'])){
      $searchdata = $_GET['search'];
      }
      $booksArray = $this->selectBooksForSelling($searchdata);

      if(isset($booksArray)){
        foreach($booksArray as $book){
          extract($book);
          if($this->isBookAvailableForUser($id)){
            $buttonsell = '<button class="alugar">Alugar <i class="fa fa-tag"></i></button>';
            if($quant <= 0){
              $buttonsell = '<button class="alugar disabled" disabled>Alugar <i class="fa fa-tag"></i></button>';
            }
          }
          else $buttonsell = '<button class="alugado" disabled>Alugado <i class="fa fa-tag"></i></button>';
          $bodycontent.="<div class='gridItem'>
                  <div class='bookImage'>
                
                  </div>
                  <div class='bookLabelContainer'>
                    <p class='bookLabel'>Título: ".$title."</p>
                    <p class='bookAuthor'>Autor: ".$author."</p>
                    <p class='bookEditor'>Editora: ".$editor."</p>
                    <p class='bookYear'>Publicado em: ".$year."</p>
                  </div>
                  <p class='bookStock".($quant>0?(null):(' soldout'))."'>".(($quant > 0)?"Disponíveis: ".$quant." unidades":"Indisponível")."</p>
                  <form action='takebook' method='post' class='bookOptions'>
                    <input type='hidden' name='id_liv' value='".$id."'>
                    ".$buttonsell."
                  </form>
                </div>";
        }
        $bodycontent .= "</div>";
        $data['bodycontent'] = $bodycontent;
        return view('bibliotemplate', $data);
      }
        return view('bibliotemplate');
    }

    public function throwBooksToSession(){
      $myModel = new BiblioBooks();

      $data = array('booksRelated' => $myModel->query('SELECT livros.* from livros join relations on relations.id_liv = livros.id join usuarios on usuarios.id = relations.id_user where usuarios.id = '.$_SESSION['userid'])->getResultArray());
      $this->session->set($data);
    }

    public function selectBooksForSelling($searchdata){
      $myModel = new BiblioBooks();

      $booksArray = $myModel->query('SELECT * FROM (SELECT * FROM livros where quant > 0 UNION SELECT * FROM livros where quant <= 0) books where title LIKE "%'.$searchdata.'%" or author LIKE "%'.$searchdata.'%" or year LIKE "%'.$searchdata.'%" or editor LIKE "%'.$searchdata.'%";')->getResultArray();
      return $booksArray;
    }

    public function loginpage($msg = null)
    {
        $bodycontent = "
        <div class='sign'>
          <div class='loginContainer'>
            <h1 class='loginTitle'>Login</h1>
            <form action='login' method='post' class='login'>
              <div class='formgroup'>
              ".((isset($msg) && !empty($msg))?$msg:null)."
              <label for='email'>Email:</label>
              <input type='email' name='email' id='email' required>
              <label for='password'>Senha:</label>
              <input type='password' name='password' id='password' required>
              <button type='submit'>Entrar</button>
              </div>
            </form>
          </div>
          <div class='separator'></div>
          <div class='signUpContainer'>
            <h1 class='loginTitle bold'>Ainda não possui uma conta? Registre-se:</h1>
            <form action='cadastro' method='post' class='login'>
              <div class='formgroup'>
              <label for='username'>Nome de usuário:</label>
              <input type='text' name='username' id='username' required>
              <label for='emailRegister'>Email:</label>
              <input type='text' name='email' id='emailRegister' required>
              <label for='password'>Senha:</label>
              <input type='password' name='password' id='passwordRegister' required>
              <button type='submit'>Registrar</button>
              </div>
            </form>
          </div>
        </div>
        ";

        $data['bodycontent'] = $bodycontent;

        return view('bibliotemplate', $data);
    }

    public function login()
    {
        $myModel = new BiblioModel();
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');
          
        // $username = $myModel->getWhere(['email'=>$email])->getRowArray()['username'];
        if(!$this->verifyUserEmailFromDatabase($email)){
          $error = "
            <div class='error'>
              O nome de usuário não confere!
            </div>
          ";
          return $this->loginpage($error);
        }
        if(!$this->verifyUserPasswordFromDatabase($password, $email)){
          $error = "
            <div class='error'>
              A senha não confere!
            </div>
          ";
          return $this->loginpage($error);
        }
        $userArray = $myModel->getWhere(['email'=>$email])->getRowArray();
        $userid = $userArray['id'];
        $username = $userArray['username'];
        $userdata = array(
          'logged_in' => true,
          'userid' => $userid,
          'username' => $email,
          'nickname' => $username,
          'booksRelated' => $myModel->query('SELECT livros.* from livros join relations on relations.id_liv = livros.id join usuarios on usuarios.id = relations.id_user')->getResultArray(),
          'admin' => $this->isAdmin($userid)
        );
        $this->session->set($userdata);
        
        return redirect()->to('index');
    }

    public function logout()
    {
        session_destroy();
        return redirect()->to('index');
    }

    public function registerpage()
    {
        $bodycontent = "
            <div class='signUpContainer' style='margin: 0 auto'>
            <h1 class='loginTitle'>Registro</h1>
            <form action='cadastro' method='post' class='login'>
              <div class='formgroup'>
              <label for='username'>Nome de usuário:</label>
              <input type='text' name='username' id='username' required>
              <label for='email'>Email:</label>
              <input type='text' name='email' id='email' required>
              <label for='password'>Senha:</label>
              <input type='password' name='password' id='password' required>
              <button type='submit'>Registrar</button>
              </div>
            </form>
          </div>
        ";
        $data['bodycontent'] = $bodycontent;

        return view('bibliotemplate', $data);
    }

    public function register()
    {   
        $myModel = new BiblioModel();

        $data = array(
          'username' => $this->request->getVar('username'),
          'email' => $this->request->getVar('email'),
          'token' => password_hash($this->request->getVar('password'), PASSWORD_BCRYPT)
        );

        $myModel->insert($data);
        
        $this->session->setFlashdata('item', 'Usuário Registrado com Sucesso!');
        print_r($_SESSION['item']);
        return redirect()->to('login');
    }

    public function verifyUserEmailFromDatabase($email)
    {
        $myModel = new BiblioModel();
        $user = $myModel->getWhere(['email' => $email])->getRowArray();
        if(empty($user)) return false;
        return true;
    }

    public function verifyUserPasswordFromDatabase($password, $email)
    {
        $myModel = new BiblioModel();
        $token = $myModel->getWhere(['email'=>$email])->getRowArray()['token'];
        if(password_verify($password, $token)) return true;
        return false;
    }

    public function listBooks($searchdata){
      $myModel = new BiblioBooks();
      $result = $myModel->query('SELECT * from livros where title LIKE "%'.$searchdata.'%" or author LIKE "%'.$searchdata.'%" or year LIKE "%'.$searchdata.'%" or editor LIKE "%'.$searchdata.'%";')->getResultArray();
      return $result;
    }

    public function booksPage()
    {
      if(!$this->checkLogin()) return redirect()->to('login');
      if(!$_SESSION['admin']) return redirect()->to('index');
      $searchdata = '';
      if(isset($_GET['search'])) $searchdata = $_GET['search'];
      $booksArray = $this->listBooks($searchdata);
      $bodycontent = "<div class='listHeader'>
        <h1 class='listTitle'>LISTA DE LIVROS REGISTRADOS</h1>
        <div class='box-search-books'>
        <input class='search-input' type='search' name='search' id='search' placeholder='Procure por um livro:'>
        <button onclick='searchData()' class='search-button'><i class='fa fa-search'></i></button>
        </div>
        <script>
          var search = document.getElementById('search');
          search.addEventListener('keydown', function(e) {if(event.key === 'Enter') searchData()}) 
          function searchData(){
            window.location = 'books?search='+search.value
          }
        </script>
        <a class='insertbutton' href='insertbooks'>Inserir +</a>
        </div>";
      $tableitems = '';
      if(isset($booksArray) && !empty($booksArray)){
        $bodycontent.="<table>
        <thead>
            <tr>
                <th>#</th>
                <th>Título</th>
                <th>Autores</th>
                <th>Ano de publicação</th>
                <th>Editora</th>
                <th>Quant.</th>
                <th colspan='2' class='toolColumn'>Ferramentas</th>
            </tr>
        </thead>
        <tbody>";
        foreach($booksArray as $row){
          $tableitems.= "<tr>";
          foreach($row as $key => $value){
            if($key != 'owners') $tableitems.= '<td>'.$value.'</td>';
          }   
          $tableitems.=
          "<td>
          <div class='form-group tools'>
          <form action='deletebooks' method='post'>
          <input type='hidden' value=".$row['id']." name='id'>
          <button type='submit'><i class='fa-solid fa-trash'></i></button>
          </form>
          <form action='editbooks' method='post'>
          <input type='hidden' value=".$row['id']." name='id'>
          <button type='submit'><i class='fa-solid fa-pen-to-square'></i></button>
          </form>
          </div>
          </td>
          </tr>";
      }
      $bodycontent.=$tableitems;
      $bodycontent.="</tbody>
      </table>";
    }
    $data['bodycontent'] = $bodycontent;
    return view('bibliotemplate', $data);
  }

    public function insertBooksPage()
    {
      if(!$this->checkLogin()) return redirect()->to('login');
      if(!$_SESSION['admin']) return redirect()->to('index');

      $bodycontent = "
      <div class='listHeader'>
      <h1 class='listTitle'>CADASTRO DE LIVROS</h1>
      </div>
      <form action='insertbooks' method='post' class='login'>
      <div class='formgroup'>
      <label for='author'>Autores:</label>
      <input type='text' name='author' id='author' required>
      <label for='title'>Título do Livro:</label>
      <input type='text' name='title' id='title' required>
      <label for='year'>Ano de publicação:</label>
      <input type='text' name='year' id='year' required>
      <label for='editor'>Editora:</label>
      <input type='text' name='editor' id='editor' required>
      <label for='quant'>Quantidade de cópias disponíveis:</label>
      <input type='number' name='quant' id='quant' required>
      <button type='submit'>Registrar</button>
      </div>
      </form>
      ";
      $data['bodycontent'] = $bodycontent;
      return view('bibliotemplate', $data);
    }

    public function getBookOwners($bookid)
    {
      $myModel = new BiblioBooks();
      $query = $myModel->query('SELECT usuarios.id from livros join relations on relations.id_liv=livros.id join usuarios on relations.id_user = usuarios.id where livros.id='.$bookid);
      $result = $query->getResultArray();
      $result = json_encode($result);
      return $result;
    }

    public function insertBooks()
    {
        $myModel = new BiblioBooks();
        $data = array(
          'title' => $this->request->getVar('title'),
          'author' => $this->request->getVar('author'),
          'year' => $this->request->getVar('year'),
          'editor' => $this->request->getVar('editor'),
          'quant' => $this->request->getVar('quant')
        );
        $myModel->insert($data);
        // $this->session->setFlashdata('item', 'Livro Inserido com Sucesso!');
        return redirect()->to('books')->with('item', 'Livro Inserido com Sucesso!');
    }

    public function deleteBooks()
    {
      $myModel = new BiblioBooks();
      $id = $this->request->getVar('id');
      $myModel->delete($id);
      return redirect()->to('books');
    }

    public function editBooksPage()
    {
      if(!$this->checkLogin()) return redirect()->to('login');

      $myModel = new BiblioBooks();
      $id = $this->request->getVar('id');
      $result = $myModel->find($id);
      $bodycontent = "
        <div class='listHeader'>
        <h1 class='listTitle'>ALTERAR LIVRO</h1>
        </div>
        <form action='updatebooks' method='post' class='login'>
        <div class='formgroup'>
        <input type='hidden' name='id' value='".$id."' id='id'>
        <label for='author'>Autores:</label>
        <input type='text' name='author' value='".$result['author']."' id='author' required>
        <label for='title'>Título do Livro:</label>
        <input type='text' name='title' value='".$result['title']."' id='title' required>
        <label for='year'>Ano de publicação:</label>
        <input type='text' name='year' value='".$result['year']."' id='year' required>
        <label for='editor'>Editora:</label>
        <input type='text' name='editor' value='".$result['editor']."' id='editor' required>
        <label for='quant'>Quantidade de cópias disponíveis:</label>
        <input type='number' name='quant' value='".$result['quant']."' id='quant' required>
        <button type='submit'>Registrar</button>
        </div>
        </form>
        ";
        $data['bodycontent'] = $bodycontent;
        return view('bibliotemplate', $data);
    }

    public function updateBooks()
    {
      $myModel = new BiblioBooks();
      $id = $this->request->getVar('id');
      $data = array(
        'title' => $this->request->getVar('title'),
        'author' => $this->request->getVar('author'),
        'year' => $this->request->getVar('year'),
        'editor' => $this->request->getVar('editor'),
        'quant' => $this->request->getVar('quant')
      );
      $myModel->update($id,$data);
      // $this->session->setFlashdata('item', 'Livro Atualizado com Sucesso!');
      return redirect()->to('books')->with('item', 'Livro Atualizado com Sucesso!');
    }

    //Ver quantidade de livros
    public function isBookAvailableForUser($bookid){
      $userbooks = $_SESSION['booksRelated'];
      foreach($userbooks as $userbook){
        if($userbook['id'] == $bookid){
          return false;
        }
      }
      return true;
    }

    public function subtractBookFromStock($bookid){
      $myModel = new BiblioBooks();
      $oldQuant = $myModel->where('id',$bookid)->first()['quant'];
      $newQuant = $oldQuant-1;
      $data = array('quant' => $newQuant);
      $myModel->update($bookid, $data);
    }

    public function linkUsersWithBooks($bookid){
      $UsersModel = new BiblioModel(); 
      $BooksModel = new BiblioBooks();
      $RelationsModel = new BiblioRelations();
      $data = array(
        'id_user' => $UsersModel->getWhere(['email' => $_SESSION['username']])->getRowArray()['id'],
        'id_liv' => $bookid
      );
      $this->subtractBookFromStock($data['id_liv']);
      $RelationsModel->insert($data);
    }

    public function takeBook(){
      $myModel = new BiblioBooks();
      $bookid = $this->request->getVar('id_liv');
      $this->linkUsersWithBooks($bookid);
      $data = array('booksRelated' => $myModel->query('SELECT livros.* from livros join relations on relations.id_liv = livros.id join usuarios on usuarios.id = relations.id_user')->getResultArray());
      $myModel->update($bookid, ['owners' => $this->getBookOwners($bookid)]);
      $this->session->set($data);
      $this->session->setFlashdata('item', "Livro emprestado com sucesso!<br>Ele já está disponível em sua biblioteca.");
      return redirect()->to('index');
    }

    public function giveBackBook(){
      $relationModel = new BiblioRelations();
      $bookModel = new BiblioBooks();
      $bookid = $this->request->getVar('id_liv');
      $relationid=$relationModel->query('select relations.id from relations where id_liv='.$bookid." and id_user=".$_SESSION['userid'])->getResultArray()[0]['id'];
      $relationModel->delete($relationid);
      $bookOldQuant = $bookModel->find($bookid)['quant'];
      $bookModel->update($bookid, ['quant' => $bookOldQuant+1, 'owners' => $this->getBookOwners($bookid)]);
      $data = array('booksRelated' => $bookModel->query('SELECT livros.* from livros join relations on relations.id_liv = livros.id join usuarios on usuarios.id = relations.id_user')->getResultArray());
      $this->session->set($data);
      $this->session->setFlashdata('item', "Livro devolvido com sucesso!<br>Ele já foi retirado de sua biblioteca.");
      return redirect()->to('index');
    }

    public function listEmprest($searchdata)
    {
      $myModel = new BiblioRelations();
      $query = $myModel->query('select relations.id, livros.title, usuarios.username, relations.dateupload from relations join livros on livros.id=relations.id_liv join usuarios on usuarios.id = relations.id_user where usuarios.username LIKE "%'.$searchdata.'%" or livros.title LIKE "%'.$searchdata.'%" or relations.id LIKE "%'.$searchdata.'%" or relations.dateupload LIKE "%'.$searchdata.'%";');
      $result = $query->getResultArray();

      return $result;
    }
    public function emprestimosPage(){
      if(!$this->checkLogin()) return redirect()->to('login');
      if(!$_SESSION['admin']) return redirect()->to('index');

      $searchdata = '';
      if(isset($_GET['search'])) $searchdata = $_GET['search'];
      $emprestArray = $this->listEmprest($searchdata);
      $bodycontent = "<div class='listHeader'>
        <h1 class='listTitle'>LISTA DE EMPRÉSTIMOS REGISTRADOS</h1>
        <div class='box-search-emprest'>
        <input class='search-input' type='search' name='search' id='search' placeholder='Procure por um empréstimo:'>
        <button onclick='searchData()' class='search-button'><i class='fa fa-search'></i></button>
        </div>
        <script>
          var search = document.getElementById('search');
          search.addEventListener('keydown', function(e) {if(event.key === 'Enter') searchData()}) 
          function searchData(){
            window.location = 'emprest?search='+search.value
          }
        </script>
        <a class='insertbutton' href='insertemprest'>Inserir +</a>
      </div>";
      $tableitems = '';
      if(isset($emprestArray) && !empty($emprestArray)){
        $bodycontent.="<table>
        <thead>
            <tr>
                <th>#</th>
                <th>Título</th>
                <th>Locatário</th>
                <th>Data do Empréstimo</th>
                <th colspan='2' class='toolColumn'>Ferramentas</th>
            </tr>
        </thead>
        <tbody>";
        foreach($emprestArray as $row){
          $tableitems.= "<tr>";
          foreach($row as $value){
            $tableitems.= '<td>'.$value.'</td>';
          }   
          $tableitems.=
          "<td>
          <div class='form-group tools'>
          <form action='deleteemprest' method='post'>
          <input type='hidden' value=".$row['id']." name='id'>
          <button type='submit'><i class='fa-solid fa-trash'></i></button>
          </form>
          <form action='editemprest' method='post'>
          <input type='hidden' value=".$row['id']." name='id'>
          <button type='submit'><i class='fa-solid fa-pen-to-square'></i></button>
          </form>
          </div>
          </td>
          </tr>";
      }
      $bodycontent.=$tableitems;
      $bodycontent.="</tbody>
      </table>";
    }
    $data['bodycontent'] = $bodycontent;
      return view('bibliotemplate', $data);
    }

    public function insertEmprestPage()
    {
      if(!$this->checkLogin()) return redirect()->to('login');

      $bodycontent = "
        <div class='listHeader'>
        <h1 class='listTitle'>EDITAR EMPRÉSTIMO</h1>
        </div>
        <form action='insertemprest' method='post' class='login'>
        <div class='formgroup'>
        <input type='hidden' name='id' value='' id='id'>
        <label for='author'>ID do Livro:</label>
        <input type='text' name='id_liv' value='' id='id_liv' required>
        <label for='title'>ID do Usuário:</label>
        <input type='text' name='id_user' value='' id='id_user' required>
        <button type='submit'>Registrar</button>
        </div>
        </form>
        ";
        $data['bodycontent'] = $bodycontent;
        return view('bibliotemplate', $data);
    }

    public function insertEmprest()
    {
        $relationModel = new BiblioRelations();
        $bookModel = new BiblioBooks();
        $data = array(
          'id_liv' => $this->request->getVar('id_liv'),
          'id_user' => $this->request->getVar('id_user'),
        );
        $relationModel->insert($data);
        $bookModel->update($data['id_liv'], ['owners' => $this->getBookOwners($data['id_liv'])]);
        return redirect()->to('emprest')->with('item', 'Empréstimo Registrado com Sucesso!');
    }

    public function editEmprestPage()
    {
      if(!$this->checkLogin()) return redirect()->to('login');

      $myModel = new BiblioRelations();
      $id = $this->request->getVar('id');
      $result = $myModel->find($id);
      $bodycontent = "
        <div class='listHeader'>
        <h1 class='listTitle'>EDITAR EMPRÉSTIMO</h1>
        </div>
        <form action='updateemprest' method='post' class='login'>
        <div class='formgroup'>
        <input type='hidden' name='id' value='".$id."' id='id'>
        <label for='author'>ID do Livro:</label>
        <input type='text' name='id_liv' value='".$result['id_liv']."' id='id_liv' required>
        <label for='title'>ID do Usuário:</label>
        <input type='text' name='id_user' value='".$result['id_user']."' id='id_user' required>
        <button type='submit'>Registrar</button>
        </div>
        </form>
        ";
        $data['bodycontent'] = $bodycontent;
        return view('bibliotemplate', $data);
    }

    public function updateEmprest()
    {
      $myModel = new BiblioRelations();
      $id = $this->request->getVar('id');
      $data = array(
        'id_liv' => $this->request->getVar('id_liv'),
        'id_user' => $this->request->getVar('id_user'),
      );
      $myModel->update($id,$data);
      // $this->session->setFlashdata('item', 'Livro Atualizado com Sucesso!');
      return redirect()->to('emprest')->with('item', 'Empréstimo Atualizado com Sucesso!');
    }

    public function deleteEmprest()
    {
      $relationModel = new BiblioRelations();
      $bookModel = new BiblioBooks();
      $id = $this->request->getVar('id');
      $bookid = $relationModel->find($id)['id_liv'];
      $bookOldQuant = $bookModel->find($bookid)['quant'];
      $relationModel->delete($id);
      $bookModel->update($bookid, ['quant' => $bookOldQuant+1, 'owners' => $this->getBookOwners($bookid)]);
      return redirect()->to('emprest');
    }
}