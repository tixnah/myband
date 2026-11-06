<?php
require __DIR__ . '/header.php';

$connexion = dbconnect();
$actionMessage = '';

if (isset($_POST['formsongaction']) && $admin) {
    $action = $_POST['formsongaction'];
    $id = filter_var($_POST['formsongid'] ?? null, FILTER_VALIDATE_INT);

    if ($action === 'add' || $action === 'modify') {
        $title = trim($_POST['title'] ?? '');
        $artist = trim($_POST['artist'] ?? '');
        $style = trim($_POST['style'] ?? '');

        if ($title === '' || $artist === '' || $style === '') {
            $actionMessage = 'Tous les champs du morceau sont obligatoires.';
        } elseif ($action === 'add') {
            $statement = $connexion->prepare(
                'INSERT INTO setlist (title, artist, style) VALUES (:title, :artist, :style)'
            );
            $statement->execute(['title' => $title, 'artist' => $artist, 'style' => $style]);
        } elseif ($id !== false) {
            $statement = $connexion->prepare(
                'UPDATE setlist SET title = :title, artist = :artist, style = :style WHERE id = :id'
            );
            $statement->execute([
                'title' => $title,
                'artist' => $artist,
                'style' => $style,
                'id' => $id,
            ]);
        }
    } elseif ($id !== false && $action === 'remove') {
        $statement = $connexion->prepare('DELETE FROM setlist WHERE id = :id');
        $statement->execute(['id' => $id]);
    } elseif ($id !== false && $action === 'uploadLyrics') {
        $file = $_FILES['lyricsPDF'] ?? null;
        if (
            is_array($file)
            && ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK
            && is_uploaded_file($file['tmp_name'])
            && mime_content_type($file['tmp_name']) === 'application/pdf'
        ) {
            $filename = bin2hex(random_bytes(16)) . '.pdf';
            $destination = __DIR__ . DIRECTORY_SEPARATOR . 'lyrics' . DIRECTORY_SEPARATOR . $filename;
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $statement = $connexion->prepare('UPDATE setlist SET lyrics = :lyrics WHERE id = :id');
                $statement->execute(['lyrics' => $filename, 'id' => $id]);
            } else {
                $actionMessage = 'Le fichier PDF n’a pas pu être enregistré.';
            }
        } else {
            $actionMessage = 'Sélectionnez un fichier PDF valide.';
        }
    } elseif ($id !== false && $action === 'removeLyrics') {
        $statement = $connexion->prepare('SELECT lyrics FROM setlist WHERE id = :id');
        $statement->execute(['id' => $id]);
        $filename = $statement->fetchColumn();

        $statement = $connexion->prepare('UPDATE setlist SET lyrics = NULL WHERE id = :id');
        $statement->execute(['id' => $id]);

        if (is_string($filename) && basename($filename) === $filename) {
            $path = __DIR__ . DIRECTORY_SEPARATOR . 'lyrics' . DIRECTORY_SEPARATOR . $filename;
            if (is_file($path) && !unlink($path)) {
                $actionMessage = 'Le lien vers les paroles a été retiré, mais le fichier doit être supprimé manuellement.';
            }
        }
    }
}


/* Querying Set List from DB */
$columns = array('title','artist','style');
$column = isset($_GET['column']) && in_array($_GET['column'], $columns) ? $_GET['column'] : $columns[0];
$sort_order = isset($_GET['order']) && strtolower($_GET['order']) == 'desc' ? 'DESC' : 'ASC';

$up_or_down = str_replace(array('ASC','DESC'), array('up','down'), $sort_order); 
$asc_or_desc = $sort_order == 'ASC' ? 'desc' : 'asc';
$sql = "SELECT * from setlist ORDER BY " .  $column . " " . $sort_order;
$rows = $connexion->query($sql)->fetchAll();

?> 

  <div class="main">
    <header class="intro">
        <p class="eyebrow">Le répertoire</p>
        <h1>Setlist</h1>
        <p>Recherchez un morceau ou triez le répertoire par titre, artiste ou style.</p>
    </header>
    <?php if ($actionMessage !== ''): ?>
        <p class="form-error" role="alert"><?= htmlspecialchars($actionMessage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
    <?php endif; ?>

    <script>
        /** Search Song Filter Function */
        function searchFunction() {
            var value = document.querySelector("#searchInput").value.toLowerCase();
            var checkbox = document.querySelector("#withLyrics");
            document.querySelectorAll("#songTable tbody tr").forEach((tr)=>{
                var search = tr.innerText.toLowerCase().indexOf(value)> -1;
                var lyrics = !checkbox || !checkbox.checked || tr.dataset.hasLyrics === "1";
                tr.style.display = (search && lyrics)?'':'none';
            });
        }

         /** Add Or Modify JS Function (using addUpdateSongForm) */
        function addormodifySong(action, id, title, artist, style) {
            // Use hidden input (formsongaction) of addUpdateSongForm to store action (add or update) ==> it will put action in $_POST['formsongaction']
            document.querySelector("#addUpdateSongForm").elements["formsongaction"].value = action;
            
            if (action=="add"){
                // set Text to "Add"
                document.querySelector('#addUpdateSongModalLabel').innerText="Ajouter un morceau";
            }
            else{
                // set Text to "Edit"
                document.querySelector('#addUpdateSongModalLabel').innerText="Modifier le morceau";
                
                // pre-fill inputs
                document.querySelector("#addUpdateSongForm").elements["title"].value = title;
                document.querySelector("#addUpdateSongForm").elements["artist"].value = artist;
                document.querySelector("#addUpdateSongForm").elements["style"].value = style;

                // Use hidden input (formsongid) of addUpdateSongForm to store song's id ==> it will put id in $_POST['formsongid']
                document.querySelector("#addUpdateSongForm").elements["formsongid"].value = id;
            }
            
            // display modal form
            let modal = document.getElementById('addUpdateSongModal');
            modal.style.display='block';
        }

        /** JS function called before submitting add/update song form to check empty values */
        function check(){
            let valid=true;

            if (document.querySelector("#addUpdateSongForm").elements["title"].value.trim() == "") {
                valid=false;
            }
            if (document.querySelector("#addUpdateSongForm").elements["artist"].value.trim() == "") {
                valid=false;
            }
            if (document.querySelector("#addUpdateSongForm").elements["style"].value.trim() == "") {
                valid=false;
            }
            
            if (!valid){
                return false;
            }
            else{
                return true;
            }
        }

         /** Remove Song JS Function (using removeSongForm) */
         function removeSong(id) {
            // Use hidden input (formsongaction) of removeSongForm to store action (remove) ==> it will put action in $_POST['formsongaction']
            document.querySelector("#removeSongForm").elements["formsongaction"].value = "remove";
            // Use hidden input (formsongid) of removeSongForm to store song's id ==> it will put id in $_POST['formsongid']
            document.querySelector("#removeSongForm").elements["formsongid"].value = id;


            // display modal form
            let modal = document.getElementById('removeSongModal');
            modal.style.display='block';
        }

        function uploadLyrics(id){
            document.querySelector("#uploadLyricsForm").elements["formsongid"].value = id;
            let modal = document.getElementById('uploadLyricsModal');
            modal.style.display='block';
        }

        function removeLyrics(id){
            document.querySelector("#removeLyricsForm").elements["formsongid"].value = id;
            let modal = document.getElementById('removeLyricsModal');
            modal.style.display='block';
        }
    </script>

    <!-- Add or Update Song Form DIV -->
    <div id="addUpdateSongModal" class="modal">
  
        <form id="addUpdateSongForm" onsubmit="return check();"  class="modal-content animate" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
            <div class="dlgheadcontainer">
                <span onclick="document.getElementById('addUpdateSongModal').style.display='none'" class="close" title="Close Modal">&times;</span>
                    <h1 id="addUpdateSongModalLabel">Modification du morceau</h1>
            </div>

            <div class="dlgcontainer">
                <input type="hidden" name="formsongaction" id="addorupdate">
                <input type="hidden" name="formsongid" id="formsongid" >


                <label for="songtitle">Titre</label>
                <input type="text" name="title" id="songtitle" placeholder="Titre du morceau" required>

                <label for="songartist">Artiste</label>
                <input type="text" name="artist" id="songartist" placeholder="Artiste ou groupe" required>

                <label for="songstyle">Style</label>
                <input type="text" name="style" id="songstyle" placeholder="Style musical" required>
                    
                <button type="submit" class="okbtn">Enregistrer</button>
                <button type="button" onclick="document.getElementById('addUpdateSongModal').style.display='none'" class="cancelbtn">Annuler</button>

            </div>

        </form>
    </div>

    <!-- Remove Song Form DIV -->
    <div id="removeSongModal" class="modal">
  
        <form id="removeSongForm" class="modal-content animate" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
            <div class="dlgheadcontainer">
                <span onclick="document.getElementById('removeSongModal').style.display='none'" class="close" title="Close Modal">&times;</span>
                    <h1 id="removeSongModalLabel">Supprimer ce morceau ?</h1>
            </div>

            <div class="dlgcontainer">
                <input type="hidden" name="formsongaction" id="addorupdate">
                <input type="hidden" name="formsongid" id="formsongid" >

                <button type="submit" class="okbtn">Oui, supprimer</button>
                <button type="button" onclick="document.getElementById('removeSongModal').style.display='none'" class="cancelbtn">Annuler</button>

            </div>

        </form>
    </div>

    <!-- Upload Lyrics Form DIV -->
    <div id="uploadLyricsModal" class="modal">
        <form id="uploadLyricsForm" class="modal-content animate" action="setlist.php" method="post" enctype="multipart/form-data">
            <div class="dlgheadcontainer">
                <span onclick="document.getElementById('uploadLyricsModal').style.display='none'" class="close" title="Close Modal">&times;</span>
                    <h1 id="uploadLyricsModalLabel">Ajouter des paroles</h1>
            </div>

            <div class="dlgcontainer">
                <input type="hidden" name="formsongaction" id="upladLyricsAction" value="uploadLyrics">
                <input type="hidden" name="formsongid" id="formsongid" >
                <label for="lyricsPDF">Choisissez un fichier PDF de paroles :</label>
                <input type="file" name="lyricsPDF" id="lyricsPDF" accept="application/pdf,.pdf" required>
                <button type="submit" class="okbtn">Téléverser</button>
                <button type="button" class="cancelbtn" onclick="document.getElementById('uploadLyricsModal').style.display='none'">Annuler</button>
            </div>
        </form>
    </div>

    <!-- Remove Lyrics Form DIV -->
    <div id="removeLyricsModal" class="modal">
        <form id="removeLyricsForm" class="modal-content animate" action="setlist.php" method="post">
            <div class="dlgheadcontainer">
                <span onclick="document.getElementById('removeLyricsModal').style.display='none'" class="close" title="Close Modal">&times;</span>
                    <h1 id="removeLyricsModalLabel">Supprimer les paroles ?</h1>
            </div>

            <div class="dlgcontainer">
                <input type="hidden" name="formsongaction" id="removeLyricsAction" value="removeLyrics">
                <input type="hidden" name="formsongid" id="formsongid" >

                <button type="submit" class="okbtn">Oui, supprimer</button>
                <button type="button" onclick="document.getElementById('removeLyricsModal').style.display='none'" class="cancelbtn">Annuler</button>

            </div>
        </form>
    </div>

    <!-- Set List Table & Search filter -->
    <div class="row">
        <div class="col-sm">
            <table id="songTable" style="width:90%;margin: auto;">
                <thead>
                    <tr>
                        <th class="headersearch" colspan="6">
                            <div class="search-controls">
                                <input type="text" class="searchinput" id="searchInput" onkeyup="searchFunction()" placeholder="Rechercher un morceau...">
                                <?php if ($member){ ?>
                                    <label><input type="checkbox" id="withLyrics" onchange="searchFunction()">Paroles disponibles uniquement</label>
                                <?php } ?>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th class="headersort"><a href="./setlist.php?column=title&order=<?= $asc_or_desc ?>">TITRE <i class="fas fa-sort<?= $column === 'title' ? '-' . $up_or_down : '' ?>"></i></a></th>
                        <th class="headersort"><a href="./setlist.php?column=artist&order=<?= $asc_or_desc ?>">ARTISTE(S) <i class="fas fa-sort<?= $column === 'artist' ? '-' . $up_or_down : '' ?>"></i></a></th>
                        <th class="headersort"><a href="./setlist.php?column=style&order=<?= $asc_or_desc ?>">STYLE <i class="fas fa-sort<?= $column === 'style' ? '-' . $up_or_down : '' ?>"></i></a></th>
                        <?php 
                        if ($member){
                            ?>
                            <th colspan="2" class="headersort">PAROLES (PDF)</th>
                            <?php
                        }
                        if ($admin){
                            ?>
                            <th colspan=2 class="headersort"><button onclick="addormodifySong('add');" type='button' class='addbtn'><i class='fa fa-plus'></i></button></th>
                            <?php
                        }
                        ?>

                    </tr>
                </thead>
                <tbody>
                <?php 
                    foreach ($rows as $row):
                        $rowId = (int) $row['id'];
                        $hasLyrics = is_string($row['lyrics']) && $row['lyrics'] !== '';
                        $title = htmlspecialchars($row['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                        $artist = htmlspecialchars($row['artist'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                        $style = htmlspecialchars($row['style'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                        $jsonFlags = JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP;
                        $jsTitle = htmlspecialchars(json_encode($row['title'], $jsonFlags), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                        $jsArtist = htmlspecialchars(json_encode($row['artist'], $jsonFlags), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                        $jsStyle = htmlspecialchars(json_encode($row['style'], $jsonFlags), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                    ?>
                        <tr data-has-lyrics="<?= $hasLyrics ? '1' : '0' ?>">
                            <td><?= $title ?></td>
                            <td><?= $artist ?></td>
                            <td><?= $style ?></td>
                            <?php if ($member): ?>
                                <td class="align-middle">
                                    <?php if ($hasLyrics): ?>
                                        <a href="download_lyrics.php?lyricsPDF=<?= rawurlencode($row['lyrics']) ?>" aria-label="Télécharger les paroles">
                                            <button type="button"><i class="fa fa-download" aria-hidden="true"></i></button>
                                        </a>
                                        <?php if ($admin): ?>
                                            <button type="button" class="cancelbtn" aria-label="Supprimer les paroles" onclick="removeLyrics(<?= $rowId ?>)"><i class="fa fa-times" aria-hidden="true"></i></button>
                                        <?php endif; ?>
                                    <?php elseif ($admin): ?>
                                        <button type="button" class="addbtn" aria-label="Ajouter les paroles" onclick="uploadLyrics(<?= $rowId ?>)"><i class="fa fa-upload" aria-hidden="true"></i></button>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                            <?php if ($admin): ?>
                                <td class="align-middle"><button type="button" class="editbtn" aria-label="Modifier le morceau" onclick="addormodifySong('modify', <?= $rowId ?>, <?= $jsTitle ?>, <?= $jsArtist ?>, <?= $jsStyle ?>)"><i class="fa fa-pen" aria-hidden="true"></i></button></td>
                                <td class="align-middle"><button type="button" class="cancelbtn" aria-label="Supprimer le morceau" onclick="removeSong(<?= $rowId ?>)"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>


</div>
  
<?php require __DIR__ . '/footer.php'; ?>