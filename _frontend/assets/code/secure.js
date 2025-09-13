class Secure {
  constructor(secret = "codetazer") {
    this.global_secret = secret;
  }

  encrypt(text, secret = this.global_secret) {
    text = String(text);
    let result = "";
    for (let i = 0; i < text.length; i++) {
      result += String.fromCharCode(
        text.charCodeAt(i) ^ secret.charCodeAt(i % secret.length)
      );
    }
    return btoa(result);
  }

  decrypt(encoded, secret = this.global_secret) {
    let text;
    try {
      text = atob(encoded);
    } catch (e) {
      return null;
    }

    let result = "";
    for (let i = 0; i < text.length; i++) {
      result += String.fromCharCode(
        text.charCodeAt(i) ^ secret.charCodeAt(i % secret.length)
      );
    }
    const printable = /^[\x20-\x7E\t\n\r]+$/;
    if (!printable.test(result)) {
      return null;
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

  mask(name) {
    let parts = name.trim().split(" ");

    if (parts.length === 1) {
      let n = parts[0];
      if (n.length <= 2) {
        return n[0].toUpperCase() + "*";
      }
      return n[0].toUpperCase()
        + "*".repeat(n.length - 2)
        + n.slice(-1).toUpperCase();
    }

    let firstName = parts[0];
    let lastName = parts[1];

    let maskedFirst;
    if (firstName.length <= 2) {
      maskedFirst = firstName[0].toUpperCase() + "*";
    } else {
      maskedFirst = firstName.substring(0, 2).toUpperCase()
        + "*".repeat(firstName.length - 3)
        + firstName.slice(-1).toUpperCase();
    }

    let maskedLast = lastName ? lastName[0].toUpperCase() + "." : "";

    return maskedFirst + " " + maskedLast;
  }

  mask_email(email) {
    let [user, domain] = email.split("@");
    if (!user || !domain) return email;

    if (user.length <= 2) {
      return user[0] + "*" + "@" + domain;
    }

    let maskedUser = user[0] + "*".repeat(user.length - 2) + user.slice(-1);

    return maskedUser + "@" + domain;
  }
}

window.SECURE = new Secure();
