import twal from "../src/mods/twal.js";
import { Tyrax } from "../src/tyrux/main.js";

let data = new FormData();
data.append("web", "adsadas");
data.append("app", "adsadas");


Tyrax.ctrql({
    action: "select",
    table: "customer",
    inspect: true,
    response: (send)=>{
        twal.ok(send.value);
    }
});