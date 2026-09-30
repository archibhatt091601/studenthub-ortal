PRACTICAL 5 - STUDENT REGISTRATION FORM

Files:
1. index.html  -> Registration form
2. style.css   -> Form styling
3. script.js   -> JavaScript validation

How to run:
1. Keep all three files in the same folder.
2. Open index.html in a browser.
3. Enter valid/invalid data to test validation.

Main validations:
- Name: letters and spaces
- Email: regular expression
- Mobile: 10 digits starting from 6-9
- Password: minimum 8 characters + strength checking
- Confirm password: must match
- Course: required selection
- Year: required radio button
- Gender: required radio button
- Terms: required checkbox

Example valid data:
Name: Archi Bhatt
Email: archi@example.com
Mobile: 9876543210
Password: Archi@1234
Confirm Password: Archi@1234
Course: Computer Engineering
Year: 2nd Year
Gender: Female
Terms: Checked

Example invalid data:
Name: A123
Email: archi@
Mobile: 12345
Password: 123
Confirm Password: 456
Course: Not selected
Year: Not selected
Gender: Not selected
Terms: Not checked

Viva points:
- HTML5 input types improve semantic input and browser support.
- Regular expressions are used to check input patterns.
- JavaScript prevents form submission when data is invalid.
- Error messages are shown beside the related fields.
- Password strength is calculated using length, uppercase, lowercase,
  number and special-character checks.
- Labels make the form easier to use with keyboard and screen readers.
