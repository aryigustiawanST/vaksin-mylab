<?php include_once('layouts/head.php') ?>

    <h2 align="center">Pendaftaran Vaksinasi</h2>
    <div class='col-md-12'>
      <form method="post" action="<?php $_SERVER['PHP_SELF']; ?>">   
        <table class="table">
          <tr>
            <td>NIK</td>
            <td>
              <input type="text" class="form-control"name="nik" required onkeypress="validate(event)" maxlength="16" required/>
            </td>
          </tr>
          <tr>
            <td width="30%">Nama</td>
            <td width="70%">
              <input type="text" class="form-control"name="nama" required/>
            </td>
          </tr>
          <tr>
            <td>Tanggal Lahir</td>
            <td>
              <input type="text" class="form-control"name="tanggal_lahir" maxlength="10" onkeyup="this.value=this.value.replace(/^(\d\d)(\d)$/g,'$1/$2').replace(/^(\d\d\/\d\d)(\d+)$/g,'$1/$2').replace(/[^\d\/]/g,'')" oninput="handleValueChange()" placeholder="dd/mm/yyyy" required/>
            </td>
          </tr>
          <tr>
            <td>No. HP</td>
            <td>
              <input type="text" class="form-control"name="no_hp" onkeypress="validate(event)" required/>
            </td>
          </tr>
          <tr>
            <td>Alamat</td>
            <td>
              <textarea class="form-control"name="alamat" required/></textarea>
            </td>
          </tr>
          <tr>
            <td>Vaksin yang diberikan pada dosis 1</td>
            <td>
              <input type="text" class="form-control"name="vaksin_dosis_satu" required/>
            </td>
          </tr>
          <tr>
            <td align="center" colspan="2">
              <button class="btn btn-success btn-block " type="submit" name="simpan" onclick="return show_confirm();">Simpan</button>
            </td>
          </tr>    
        </table>
      </form>
    </div>

<?php include_once('layouts/footer.php') ?>