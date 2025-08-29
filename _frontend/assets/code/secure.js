class Secure {
  constructor(secret = "codetazer") {
    this.global_secret = secret;
  }

  encrypt(text, secret = this.global_secret) {
    let result = "";
    for (let i = 0; i < text.length; i++) {
      result += String.fromCharCode(
        text.charCodeAt(i) ^ secret.charCodeAt(i % secret.length)
      );
    }
    return btoa(result);
  }

  decrypt(encoded, secret = this.global_secret) {
    let text = atob(encoded);
    let result = "";
    for (let i = 0; i < text.length; i++) {
      result += String.fromCharCode(
        text.charCodeAt(i) ^ secret.charCodeAt(i % secret.length)
      );
    }
    return result;
  }

  set_item(key, value, secret = this.global_secret) {
    if (typeof value === "undefined" || value === "") {
      return null;
    }
    const secureValue = this.encrypt(value, secret);
    localStorage.setItem(key, secureValue);
  }

  get_item(key, secret = this.global_secret) {
    const stored = localStorage.getItem(key);
    if (!stored) {
      return null;
    }
    return this.decrypt(stored, secret);
  }

  remove_item(key) {
    localStorage.removeItem(key);
  }
}

window.SECURE = new Secure();
