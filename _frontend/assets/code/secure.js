
function encrypt(text, secret="codetazer") {
  let result = "";
  for (let i = 0; i < text.length; i++) {
    result += String.fromCharCode(
      text.charCodeAt(i) ^ secret.charCodeAt(i % secret.length)
    );
  }
  return btoa(result); 
}

function decrypt(encoded, secret="codetazer") {
  let text = atob(encoded); 
  let result = "";
  for (let i = 0; i < text.length; i++) {
    result += String.fromCharCode(
      text.charCodeAt(i) ^ secret.charCodeAt(i % secret.length)
    );
  }
  return result;
}


