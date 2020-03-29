






function goToDonationPage() {
  location.href = "../html/donate.html";
}

function validateEmail(field) {
  // posso aggiungere la lunghezza minima, se concorde ai
  // generatori di dati dei test
  // obbligatori del prof. Valenza

  // return (field == "") ? "No Surname was entered.\n" : ""
  return "error";
}

function validatePassword(field) {
  return (field == "") ? "No password was entered.\n" : "";
}

function validateNickname(field) {
  return (field == "") ? "No nickname was entered.\n" : "";
}

function validateAmount(field) {
  if (isNaN(field)) return "No number was entered.\n";
  else if (field < 0) return "Amount needs to be a positive number.\n";
  return "";
}

function validateMessage(field) {
  return (field == "") ? "No message was entered.\n" : "";
}
