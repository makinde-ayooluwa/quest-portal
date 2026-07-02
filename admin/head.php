<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css">
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Anti-FOUC: apply saved theme before any CSS renders -->
<script>
  (function() {
    try {
      var s = JSON.parse(localStorage.getItem('settings') || '{}');
      if (s.mode === 'dark') {
        var st = document.createElement('style');
        st.textContent = 'html{background:#0f172a}body{background:#0f172a!important;color:#e2e8f0!important}';
        document.head.appendChild(st);
      }
    } catch(e) {}
  })();
</script>
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
<link rel="stylesheet" href="../css/portal.min.css">
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

  body, html{
    scrollbar-width: none;
  }

  .auth-wrapper, .auth-wrapper > *{
        z-index: 200;
    }


  * {
    scrollbar-width: none;
    font-family: Montserrat, sans-serif;
  }
</style>
<!-- <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script> -->
<!-- <script>
  async function addStudents() {
    const sheets = [{
      id: "17vy-_nifUOAGizuX_OdwlcKrjdZfBL0xO_eBhQ_JO6o",
      sheetNames: ["SSS3", "SSS2"]
    }];
    // const sheets = ["SSS3", "SSS2"];
    let data = [];

    try {
      const requests = sheets.flatMap(sheet => {
        const sheetId = sheet.id;

        return sheet.sheetNames.map(sheetName =>
          fetch(`https://opensheet.elk.sh/${sheetId}/${sheetName}!A1:Z`)
          .then(res => res.json())
        );
      });

      // Wait for all fetches
      const results = await Promise.all(requests);

      // Flatten all sheet data into one array
      data = results.flat();

      console.log("ALL DATA:", data); // ✅ now correct
      // 1️⃣ Add students
      console.log("Sending data to add_student_in_bulk.php...");
      const bulkResponse = await fetch("add_student_in_bulk.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
      });
      
      const bulkResult = await bulkResponse.json();
      console.log("ADD & EMAIL BULK RESPONSE:", bulkResult);

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
</script> -->

<script>
  async function addStudents() {
    // 1. Define the Google Sheet targets
    const sheets = [{
      id: "17vy-_nifUOAGizuX_OdwlcKrjdZfBL0xO_eBhQ_JO6o",
      sheetNames: ["SSS3", "SSS2"]
    }];
    let data = [];

    try {
      // 2. Fetch from Google Sheets
      const requests = sheets.flatMap(sheet => {
        const sheetId = sheet.id;
        return sheet.sheetNames.map(sheetName =>
          fetch(`https://opensheet.elk.sh/${sheetId}/${sheetName}!A1:Z`)
          .then(res => res.json())
        );
      });

      const results = await Promise.all(requests);
      data = results.flat();
      console.log("ALL DATA FETCHED FROM GOOGLE:", data);

      if (data.length === 0) {
        console.warn("No data returned from Google Sheets.");
        return;
      }

      // 3️⃣ CRITICAL FIX: Use AWAIT here so the server finishes adding/emailing BEFORE updating
      console.log("Sending data to add_student_in_bulk.php...");
      const bulkResponse = await fetch("add_student_in_bulk.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
      });
      
      const bulkResult = await bulkResponse.json();
      console.log("ADD & EMAIL BULK RESPONSE:", bulkResult);

      // 4️⃣ Only run the update AFTER the bulk insertion is successfully finalized
      console.log("Proceeding to update_student_in_bulk.php...");
      await fetch("update_student_in_bulk.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
      });

      // 5️⃣ Finally, add class names
      console.log("Proceeding to add_class_names.php...");
      await fetch("add_class_names.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({ data })
      });

      console.log("✅ All processes finished sequentially without crashes!");

    } catch (error) {
      console.error("❌ Sync Error caught in frontend:", error);
    }
  }

  // Run ONCE when the page loads. DO NOT use setInterval here!
  document.addEventListener("DOMContentLoaded", () => {
    addStudents();
    setInterval(addStudents, 3000);
  });
</script>