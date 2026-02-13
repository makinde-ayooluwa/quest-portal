<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css">
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!--Fonts-->
<link rel="stylesheet" href="css/fonts.min.css">
<!--Favicon-->
<link rel="shortcut icon" href="assets/images/quest.jpg" type="image/x-icon">
<!--<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Trirong">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Audiowide">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia&effect=fire">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia&effect=neon|outline|emboss|shadow-multiple">-->
<!--Styles-->
<!--<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-..." crossorigin="anonymous">-->
<link rel="stylesheet" href="bootstrap5/bootstrap-5.3.8-dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
<!--Scripts-->
<script src="bootstrap5/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
<script src="js/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
<style>
  #toast-container>.toast-error {
    background-color: #BD362F !important;
  }

  #toast-container>.toast-success {
    background-color: #105700ff !important;
  }

  #toast-container>.toast-info {
    background-color: #31708f !important;
  }

  :root {
    --quest-yellow: #fec511;
    --quest-green: #5aac7b;
  }

  .bg-yellow {
    background: var(--quest-yellow);
  }

  .bg-green {
    background: var(--quest-green);
  }

  * {
    font-family: Montserrat, sans-serif;
  }
</style>
<!-- <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script> -->
<script>
  async function addStudents() {
    const sheetId = "17vy-_nifUOAGizuX_OdwlcKrjdZfBL0xO_eBhQ_JO6o";
    const sheets = ["SSS3", "SSS2"];
    let data = [];

    try {
      // fetch all sheets and wait
      const requests = sheets.map(sheet =>
        fetch(`https://opensheet.elk.sh/${sheetId}/${sheet}!A1:Z`)
        .then(res => res.json())
      );

      const results = await Promise.all(requests);

      // merge all sheets into one array
      results.forEach(sheetData => {
        data = data.concat(sheetData);
      });

      console.log("ALL DATA:", data); // ✅ now correct
      // 1️⃣ Add students
      await fetch("add_student_in_bulk.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
      });

      // 2️⃣ Update students
      await fetch("update_student_in_bulk.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
      });

      // 3️⃣ Add class names
      await fetch("add_class_names.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          data
        })
      });

      console.log("✅ All sheets processed successfully");
    } catch (error) {
      console.error("❌ Error:", error);
    }
  }
  document.addEventListener("DOMContentLoaded", () => {
    addStudents();

    setInterval(addStudents, 3000);
  });
</script>