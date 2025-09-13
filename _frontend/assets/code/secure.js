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
    let fullName = name;
    let parts = fullName.trim().split(" ");

    if (parts.length === 1) {
      let name = parts[0];
      if (name.length <= 3) {
        return name[0].toUpperCase() + "**";
      } else {
        return name.substring(0, 2).toUpperCase()
          + "**"
          + name.slice(-1).toUpperCase();
      }
    }

    let firstName = parts[0];
    let lastName = parts[1];

    let maskedFirst;
    if (firstName.length <= 3) {
      maskedFirst = firstName[0].toUpperCase() + "**";
    } else {
      maskedFirst = firstName.substring(0, 2).toUpperCase()
        + "**"
        + firstName.slice(-1).toUpperCase();
    }

    let maskedLast = lastName ? lastName[0].toUpperCase() + "." : "";

    return maskedFirst + " " + maskedLast;
  }

  mask_email(email) {
    let[user, domain] = email.split("@");

    if (!user || !domain) return email;

    let maskedUser;
    if (user.length <= 3) {
      maskedUser = user[0] + "**";
    } else {
      maskedUser = user.substring(0, 2) + "**" + user.slice(-1);
    }

    return maskedUser + "@" + domain;
  }
}

window.SECURE = new Secure();
