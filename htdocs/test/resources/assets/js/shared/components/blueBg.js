import $ from 'jquery';

// Written by designers
// Moved here only with eslint fixes

let WIDTH;
let HEIGHT;
let con;
const pxs = [];
const rint = 60;

export default {

  render(h) {
    return h('canvas', [], []);
  },

  mounted() {
    WIDTH = window.innerWidth;
    HEIGHT = window.innerHeight;
    $(this.$el).attr('width', WIDTH).attr('height', HEIGHT);
    con = this.$el.getContext('2d');
    for (let i = 0; i < 100; i += 1) {
      // eslint-disable-next-line no-use-before-define
      pxs[i] = new Circle();
      pxs[i].reset();
    }
    // eslint-disable-next-line no-use-before-define
    setInterval(draw, rint);
  },

};

function draw() {
  con.clearRect(0, 0, WIDTH, HEIGHT);
  pxs.forEach((item) => {
    item.fade();
    item.move();
    item.draw();
  });
}

function Circle() {
  this.s = {
    ttl: 16000,
    xmax: 3,
    ymax: 2,
    rmax: 200,
    rt: 1,
    xdef: 960,
    ydef: 540,
    xdrift: 2,
    ydrift: 2,
    random: true,
    blink: true,
  };

  // fill vars
  const crFill = [
    ['rgba(255,255,255,0)', 'rgba(255,255,255,1)'],
    ['rgba(255,255,255,0)', 'rgba(255,255,255,1)'],
    ['rgba(255,255,255,0)', 'rgba(255,255,255,1)'],
    ['rgba(255,255,255,0)', 'rgba(255,255,255,1)'],
  ];

  // opacity var
  const opacityFill = `.${Math.floor(Math.random() * 5) + 1}`;

  this.reset = () => {
    this.x = (this.s.random ? WIDTH * Math.random() : this.s.xdef);
    this.y = (this.s.random ? HEIGHT * Math.random() : this.s.ydef);
    this.r = ((this.s.rmax - 1) * Math.random()) + 1;
    this.dx = (Math.random() * this.s.xmax) * (Math.random() < 0.5 ? -1 : 1);
    this.dy = (Math.random() * this.s.ymax) * (Math.random() < 0.5 ? -1 : 1);
    this.hl = (this.s.ttl / rint) * (this.r / this.s.rmax);
    this.rt = Math.random() * this.hl;
    this.s.rt = Math.random() + 1;
    this.stop = (Math.random() * 0.2) + 0.4;
    this.s.xdrift *= Math.random() * (Math.random() < 0.5 ? -1 : 1);
    this.s.ydrift *= Math.random() * (Math.random() < 0.5 ? -1 : 1);
    this.opacityFill = opacityFill;

    this.currentColor = Math.floor(Math.random() * crFill.length);
  };

  this.fade = () => {
    this.rt += this.s.rt;
  };

  this.draw = () => {
    if (this.s.blink && (this.rt <= 0 || this.rt >= this.hl)) {
      this.s.rt = this.s.rt * -1;
    }
    else if (this.rt >= this.hl) {
      this.reset();
    }
    con.beginPath();
    con.arc(this.x, this.y, this.r, 0, Math.PI * 2, true);
    con.globalAlpha = opacityFill;
    const newo = 1 - (this.rt / this.hl);
    const cr = this.r * newo;

    const gradient = con.createRadialGradient(
      this.x, this.y, 0, this.x, this.y, (cr <= 0 ? 1 : cr)
    );
    gradient.addColorStop(0.0, crFill[(this.currentColor)][1]);
    gradient.addColorStop(0.7, crFill[(this.currentColor)][1]);
    gradient.addColorStop(1.0, crFill[(this.currentColor)][0]);

    con.fillStyle = gradient;
    con.fill();

    con.closePath();
  };

  this.move = () => {
    this.x += (this.rt / this.hl) * this.dx;
    this.y += (this.rt / this.hl) * this.dy;
    if (this.x > WIDTH || this.x < 0) {
      this.dx *= -1;
    }
    if (this.y > HEIGHT || this.y < 0) {
      this.dy *= -1;
    }
  };

  this.getX = () => this.x;
  this.getY = () => this.y;
}
