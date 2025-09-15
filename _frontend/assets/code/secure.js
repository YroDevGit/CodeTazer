class Secure {
  constructor(secret = "codetazer") {
    this.global_secret = secret;
    this.maskChar = "●";
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

  storage_set(key, value, secret = this.global_secret) {
    if (typeof value === "undefined" || value === "") {
      return null;
    }
    const secureValue = this.encrypt(value, secret);
    localStorage.setItem(key, secureValue);
  }

  storage_get(key, secret = this.global_secret) {
    const stored = localStorage.getItem(key);
    if (!stored) {
      return null;
    }
    return this.decrypt(stored, secret);
  }

  storage_remove(key) {
    localStorage.removeItem(key);
  }

  storage_clear(){
    localStorage.clear();
  }

  mask(name, complete = true) {
    if (complete) {
      return this.mask_full(name);
    }
    let parts = name.trim().split(" ");

    function maskWord(word) {
      if (word.length <= 2) {
        return word[0].toUpperCase() + this.maskChar;
      }

      let interval = word.length >= 8 ? 3 : 2;

      return word
        .split("")
        .map((ch, i) => {
          if (i === 0) return ch.toUpperCase();
          if (i === word.length - 1) return ch.toUpperCase();
          if (i % interval === 0) return ch.toLowerCase();
          return this.maskChar;
        })
        .join("");
    }

    if (parts.length === 1) {
      return maskWord(parts[0]);
    }

    let firstName = parts[0];
    let lastName = parts[1];

    let maskedFirst = maskWord(firstName);
    let maskedLast = lastName ? lastName[0].toUpperCase() + "." : "";

    return maskedFirst + " " + maskedLast;
  }

  mask_email(email) {
    let [user, domain] = email.split("@");
    if (!user || !domain) return email;

    if (user.length <= 2) {
      return user[0] + this.maskChar + "@" + domain;
    }

    let interval = user.length >= 8 ? 3 : 2;

    let maskedUser = user
      .split("")
      .map((ch, i) => {
        if (i === 0) return ch;
        if (i === user.length - 1) return ch;
        if (i % interval === 0) return ch;
        return this.maskChar;
      })
      .join("");

    return maskedUser + "@" + domain;
  }

  mask_word(word) {
    if (word.length <= 2) {
      return word[0] + this.maskChar;
    }

    let interval = word.length >= 8 ? 3 : 2;

    return word
      .split("")
      .map((ch, i) => {
        if (i === 0) return ch;
        if (i === word.length - 1) return ch;
        if (i % interval === 0) return ch;
        return this.maskChar;
      })
      .join("");
  }

  mask_full(fullName) {
    return fullName
      .trim()
      .split(/\s+/)
      .map(word => this.mask_word(word))
      .join(" ");
  }
}

window.SECURE = new Secure();
