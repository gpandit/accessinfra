const { useState: __DM_useState, useEffect: __DM_useEffect, useRef: __DM_useRef } = React;

const seg = (x1,y1,x2,y2) => [x1,y1,x2,y2];
const rectSeg = (x1,y1,x2,y2) => [seg(x1,y1,x2,y1),seg(x2,y1,x2,y2),seg(x2,y2,x1,y2),seg(x1,y2,x1,y1)];
const crossSeg = (cx,cy,r) => [seg(cx-r,cy,cx+r,cy),seg(cx,cy-r,cx,cy+r)];
const polySeg = (pts) => pts.map((p,i) => seg(p[0],p[1],pts[(i+1)%pts.length][0],pts[(i+1)%pts.length][1]));

function sampleSegments(segments, count) {
  const lens = segments.map(([x1,y1,x2,y2]) => Math.hypot(x2-x1,y2-y1) || 0.0001);
  const total = lens.reduce((a,b) => a+b, 0);
  const pts = [];
  for (let i=0; i<count; i++) {
    let d = (i/(count-1)) * total;
    let idx = 0;
    while (idx < lens.length-1 && d > lens[idx]) { d -= lens[idx]; idx++; }
    const [x1,y1,x2,y2] = segments[idx];
    const t = d / lens[idx];
    pts.push([x1+(x2-x1)*t, y1+(y2-y1)*t]);
  }
  return pts;
}

const INFRA_SHAPES = [
  // Bridge
  [
    seg(20,95,180,95),
    seg(60,40,60,95), seg(140,40,140,95),
    seg(60,40,40,95), seg(60,40,80,95),
    seg(140,40,120,95), seg(140,40,160,95),
    seg(30,115,50,115), seg(70,115,90,115), seg(110,115,130,115), seg(150,115,170,115),
  ],
  // School
  [
    seg(40,55,100,20), seg(100,20,160,55),
    ...rectSeg(45,55,155,115),
    seg(90,90,90,115), seg(110,90,110,115), seg(90,90,110,90),
    ...crossSeg(65,75,8), ...crossSeg(135,75,8),
  ],
  // Hospital
  [
    ...rectSeg(40,30,160,115),
    ...crossSeg(100,68,18),
    ...crossSeg(60,45,6), ...crossSeg(140,45,6),
    seg(70,115,70,95), seg(130,115,130,95),
  ],
  // Highway
  [
    seg(60,115,100,30), seg(140,115,100,30),
    seg(95,110,98,96), seg(97,90,99,78), seg(98.5,75,100,65), seg(99.5,60,100,50),
    seg(170,40,170,80), ...rectSeg(158,28,182,42),
  ],
  // Power grid
  [
    seg(100,30,100,115),
    seg(70,45,130,45), seg(75,65,125,65),
    seg(85,45,100,75), seg(115,45,100,75), seg(88,65,100,90), seg(112,65,100,90),
    seg(100,115,75,140), seg(100,115,125,140),
  ],
];

const INDIA_STATE_SHAPES = [
  // Karnataka
  polySeg([[90,15],[120,10],[135,35],[125,55],[140,80],[120,110],[100,140],[75,135],[60,100],[70,60],[55,35],[75,20]]),
  // Telangana
  polySeg([[70,30],[110,25],[140,45],[145,80],[120,110],[85,115],[55,95],[50,60],[60,40]]),
  // Andhra Pradesh
  polySeg([[40,40],[80,25],[130,30],[160,55],[150,80],[170,110],[140,130],[100,120],[60,130],[45,95],[55,70]]),
  // Tamil Nadu
  polySeg([[50,20],[140,15],[150,45],[120,80],[100,120],[90,145],[70,120],[55,80],[45,45]]),
  // Maharashtra
  polySeg([[30,30],[150,20],[170,55],[150,90],[100,110],[60,100],[35,75]]),
  // Uttar Pradesh
  polySeg([[15,55],[60,30],[120,25],[170,40],[185,65],[160,85],[110,95],[60,90],[25,80]]),
  // Delhi
  polySeg([[85,55],[115,50],[125,75],[110,100],[80,95],[70,72]]),
];

const __DM_REDUCE_MOTION = typeof window !== 'undefined' && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function DotMorph({ shapes, labels, fullBleed, count=320, intervalMs=2600 }) {
  const canvasRef = __DM_useRef(null);
  const wrapRef = __DM_useRef(null);
  const [shapeIdx, setShapeIdx] = __DM_useState(0);

  __DM_useEffect(() => {
    const canvas = canvasRef.current;
    const wrap = wrapRef.current;
    const ctx = canvas.getContext('2d');
    const BOX = { w:200, h:150 };
    const shapesPx = shapes.map(s => sampleSegments(s, count));
    const particles = shapesPx[0].map(([x,y]) => ({ x, y, tx:x, ty:y }));
    let idx = 0;
    let raf, advance;

    function resize() {
      const rect = wrap.getBoundingClientRect();
      const dpr = window.devicePixelRatio || 1;
      canvas.width = rect.width * dpr;
      canvas.height = rect.height * dpr;
      canvas.style.width = rect.width+'px';
      canvas.style.height = rect.height+'px';
      ctx.setTransform(dpr,0,0,dpr,0,0);
    }
    resize();
    window.addEventListener('resize', resize);

    function draw() {
      const rect = wrap.getBoundingClientRect();
      ctx.clearRect(0,0,rect.width,rect.height);
      particles.forEach(p => { p.x += (p.tx-p.x)*0.07; p.y += (p.ty-p.y)*0.07; });
      const pts = particles.map(p => [ (p.x/BOX.w)*rect.width, (p.y/BOX.h)*rect.height ]);
      const threshold = rect.width*0.045;
      const thresholdSq = threshold*threshold;
      ctx.lineWidth = 1;
      for (let i=0; i<pts.length; i++) {
        for (let j=i+1; j<pts.length; j++) {
          const dx = pts[i][0]-pts[j][0], dy = pts[i][1]-pts[j][1];
          const dSq = dx*dx+dy*dy;
          if (dSq < thresholdSq) {
            const d = Math.sqrt(dSq);
            ctx.globalAlpha = (1 - d/threshold) * 0.5;
            ctx.strokeStyle = 'rgba(255,255,255,0.4)';
            ctx.beginPath(); ctx.moveTo(pts[i][0],pts[i][1]); ctx.lineTo(pts[j][0],pts[j][1]); ctx.stroke();
          }
        }
      }
      ctx.globalAlpha = 1;
      pts.forEach((p,i) => {
        ctx.beginPath();
        ctx.arc(p[0], p[1], 1.6, 0, Math.PI*2);
        ctx.fillStyle = i%2===0 ? 'rgba(255,255,255,0.95)' : 'rgba(255,255,255,0.7)';
        ctx.fill();
      });
      raf = requestAnimationFrame(draw);
    }

    if (__DM_REDUCE_MOTION) {
      draw();
      cancelAnimationFrame(raf);
      return () => window.removeEventListener('resize', resize);
    }

    advance = setInterval(() => {
      idx = (idx+1) % shapesPx.length;
      setShapeIdx(idx);
      const targets = shapesPx[idx];
      particles.forEach((p,i) => { p.tx = targets[i][0]; p.ty = targets[i][1]; });
    }, intervalMs);
    draw();

    return () => {
      cancelAnimationFrame(raf);
      clearInterval(advance);
      window.removeEventListener('resize', resize);
    };
  }, [shapes, count, intervalMs]);

  const wrapStyle = fullBleed
    ? { position:'absolute', inset:0, zIndex:0, overflow:'hidden' }
    : { position:'relative', width:'100%', aspectRatio:'4/3', borderRadius:20, overflow:'hidden', background:'linear-gradient(135deg,#14b8a6 0%,#0f766e 100%)', boxShadow:'var(--shadow-lg)' };

  return (
    <div ref={wrapRef} role="img" aria-label={labels ? `Animated illustration cycling through: ${labels.join(', ')}` : 'Animated decorative illustration'} style={wrapStyle}>
      <canvas ref={canvasRef} style={{ position:'absolute', inset:0 }} />
      {labels && (
        <div style={{ position:'absolute', right: fullBleed?22:'auto', left: fullBleed?'auto':18, bottom:18, background:'rgba(255,255,255,0.18)', backdropFilter:'blur(6px)', color:'#fff', fontSize:13, fontWeight:700, padding:'7px 14px', borderRadius:999, letterSpacing:'0.03em' }}>
          {labels[shapeIdx]}
        </div>
      )}
    </div>
  );
}

window.DotMorph = DotMorph;
window.AI_SHAPES = { INFRA: INFRA_SHAPES, INDIA_STATES: INDIA_STATE_SHAPES };
