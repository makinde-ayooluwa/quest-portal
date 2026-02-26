# TODO - Convert CSV URL to Array

## Task: Make the CSV URL an array to allow multiple Google Sheets to be fetched for student results

### Steps:
- [x] 1. Analyze the current code in view_result.php
- [ ] 2. Convert the single GOOGLE_SHEETS_CSV_URL constant to an array GOOGLE_SHEETS_CSV_URLS
- [ ] 3. Modify the code to iterate through all URLs and fetch results for each student
- [ ] 4. Merge/combine results from all sheets
- [ ] 5. Update the display logic to show results from all sheets

### Implementation Details:
- Replace single URL constant with array of URLs
- Update getStudentResultsFromSheet to handle multiple sheets
- Aggregate results from all sheets
- Display combined results in the view
