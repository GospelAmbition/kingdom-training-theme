import{j as e}from"./vendor-query-sc-3RV5c.js";import{r as m,L as k,e as le,u as ce}from"./vendor-react-BsyC4uYt.js";import{g as de,u as O,r as me,a as ue,p as pe,S as ge,b as M}from"./main-Cm2q55zG.js";import{C as Q}from"./ContentCard-BqK8UsLU.js";import{M as ee,A as K}from"./vendor-ui-BIzQpSM7.js";import{N as xe}from"./NeuralBackground-C8naqV0X.js";import{S as fe}from"./SEO-BQWZoWKI.js";import{u as te,f as re}from"./useArticles-CBe8LjoM.js";import{u as be,f as he}from"./useTools-C5VbBcSh.js";import{u as ye}from"./useCourses-DS-gXvu7.js";import"./vendor-helmet-BvUtKEoN.js";const ne=["Create a discipleship training video that sparks movements in unreached communities...","Generate an engaging video teaching biblical principles for multiplying disciples...","Produce a testimony video showing how media catalyzes church planting movements...","Make an interactive video equipping believers to share the Gospel through digital tools...","Create a training series on facilitating discovery Bible studies in oral cultures...","Generate content showing how one faithful disciple can multiply into thousands..."];function we(){return typeof window>"u"?!1:window.matchMedia("(prefers-reduced-motion: reduce)").matches}function ve(){const x=m.useRef(null),l=m.useRef(null),r=m.useRef(null),g=m.useRef(null),s=m.useRef(null),u=m.useRef(null),i=m.useRef("desktop"),a=m.useRef(!0),c=m.useRef(0),v=m.useRef(0),j=m.useRef(!1),I=m.useRef([]),N=m.useRef(0),E=6,C=m.useRef();return m.useEffect(()=>{if(we())return;const T=(t="data",p=!1)=>{if(!r.current||!l.current||!s.current||!x.current)return;const b=document.createElement("div");b.className=`particle ${t}`;const n=l.current.getBoundingClientRect(),f=x.current.getBoundingClientRect(),o=n.left-f.left+Math.random()*n.width,_=n.top-f.top+Math.random()*n.height,S=t==="create"?f.height*.15:f.height*.5;let $;p||Math.random()<.6?$=f.width*.2+(Math.random()-.5)*200:$=f.width*.75+(Math.random()-.5)*100;const L={element:b,x:o,y:_,targetX:$,targetY:S,duration:2e3+Math.random()*1e3,startTime:performance.now(),size:3+Math.random()*4,opacity:0};b.style.width=`${L.size}px`,b.style.height=`${L.size}px`,b.style.left=`${L.x}px`,b.style.top=`${L.y}px`,b.style.opacity="0",r.current.appendChild(b),I.current.push(L)},h=(t,p)=>{const b=p-t.startTime,n=Math.min(b/t.duration,1),f=n<.5?2*n*n:1-Math.pow(-2*n+2,2)/2,o=(t.x+t.targetX)/2+(Math.random()-.5)*100;return t.x=t.x+(o-t.x)*f*.5,t.y=t.y+(t.targetY-t.y)*f,n<.2?t.opacity=n/.2:n>.8?t.opacity=(1-n)/.2:t.opacity=.8,t.element.style.left=`${t.x}px`,t.element.style.top=`${t.y}px`,t.element.style.opacity=t.opacity.toString(),n>.4&&n<.5&&Math.random()>.95&&d(),n>=1?(t.element.remove(),!1):!0},d=()=>{if(!g.current||!s.current)return;if(N.current>=E){const o=g.current.querySelectorAll(".video-frame");o.length>0&&o[0].remove()}const t=document.createElement("div");t.className="video-frame";const p=N.current*137.5*(Math.PI/180),b=120+N.current*15,n=Math.cos(p)*b,f=Math.sin(p)*b;t.style.transform=`translate(-50%, -50%) translate(${n}px, ${f}px) scale(0)`,t.style.opacity="0",g.current.appendChild(t),setTimeout(()=>{t.parentElement&&(t.style.transition="all 1.6s cubic-bezier(0.34, 1.56, 0.64, 1)",t.style.transform=`translate(-50%, -50%) translate(${n}px, ${f}px) scale(1)`,t.style.opacity="1")},50),setTimeout(()=>{if(!s.current||!x.current||!t.parentElement)return;const o=s.current.getBoundingClientRect(),_=x.current.getBoundingClientRect(),S=t.getBoundingClientRect(),$=o.left-_.left+o.width/2-(S.left-_.left)-S.width/2,L=o.top-_.top+o.height/2-(S.top-_.top)-S.height/2;t.style.transition="all 2.5s cubic-bezier(0.4, 0.0, 0.2, 1)",t.style.transform=`translate(-50%, -50%) translate(${$}px, ${L}px) scale(2.5)`,t.style.opacity="0",setTimeout(()=>{t.parentElement&&t.remove()},2500)},3500),N.current++},y=()=>{const t=8+Math.floor(Math.random()*5);for(let p=0;p<t;p++)setTimeout(()=>{d()},p*80);for(let p=0;p<25;p++)setTimeout(()=>{const b=p<15;T("data",b)},p*40)},A=()=>{const t=["desktop","tablet","mobile"],b=(t.indexOf(i.current)+1)%t.length;i.current=t[b],u.current&&u.current.setAttribute("data-platform",i.current)},B=()=>{u.current&&(u.current.style.animation="none",u.current.offsetWidth,u.current.style.transition="opacity 0.8s ease-out",u.current.style.opacity="0.2",setTimeout(()=>{u.current&&(u.current.style.opacity="1")},800))},Y=()=>{if(j.current||!l.current)return;j.current=!0,A(),B(),y();const t=ne[c.current];v.current=0;const p=setInterval(()=>{l.current&&(v.current<t.length?(l.current.textContent=t.substring(0,v.current+1),v.current++,v.current%3===0&&T()):(clearInterval(p),j.current=!1,setTimeout(()=>{l.current&&(c.current=(c.current+1)%ne.length,l.current.style.opacity="0",setTimeout(()=>{l.current&&(l.current.textContent="",l.current.style.opacity="1",Y())},1e3))},5e3)))},50)},q=t=>{if(!a.current){C.current=requestAnimationFrame(q);return}for(let p=I.current.length-1;p>=0;p--)h(I.current[p],t)||I.current.splice(p,1);C.current=requestAnimationFrame(q)};setTimeout(()=>{Y(),C.current=requestAnimationFrame(q)},500),setTimeout(()=>{u.current&&(u.current.style.animation="none")},5e3);const w=setInterval(()=>{j.current&&T()},80),F=setInterval(()=>{j.current&&Math.random()>.7&&d()},1200);setTimeout(()=>{for(let t=0;t<20;t++)setTimeout(()=>T(),t*50)},1e3);let z=null;return x.current&&typeof IntersectionObserver<"u"&&(z=new IntersectionObserver(([t])=>{a.current=t.isIntersecting},{threshold:.1}),z.observe(x.current)),()=>{C.current&&cancelAnimationFrame(C.current),z&&z.disconnect(),clearInterval(w),clearInterval(F),I.current.forEach(t=>t.element.remove())}},[]),e.jsxs("div",{ref:x,className:"hidden md:block absolute inset-0 overflow-hidden pointer-events-none z-0",style:{opacity:.5},children:[e.jsx("style",{children:`
                .genmap-bg-container .text-prompt {
                    font-size: 18px;
                    color: #e2e8f0;
                    line-height: 1.6;
                    text-align: left;
                    padding: 20px 30px;
                    background: rgba(15, 23, 42, 0.7);
                    border: 1px solid rgba(59, 130, 246, 0.3);
                    border-radius: 12px;
                    box-shadow: 0 4px 20px rgba(59, 130, 246, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.1);
                    backdrop-filter: blur(10px);
                    min-height: 60px;
                    position: relative;
                    transition: opacity 0.3s;
                }

                .genmap-bg-container .particle {
                    position: absolute;
                    border-radius: 50%;
                    pointer-events: none;
                    will-change: transform, opacity;
                }

                .genmap-bg-container .particle.data {
                    background: #3b82f6;
                    box-shadow: 0 0 10px rgba(59, 130, 246, 0.8), 0 0 20px rgba(59, 130, 246, 0.4);
                }

                .genmap-bg-container .particle.process {
                    background: #8b5cf6;
                    box-shadow: 0 0 10px rgba(139, 92, 246, 0.8), 0 0 20px rgba(139, 92, 246, 0.4);
                }

                .genmap-bg-container .particle.create {
                    background: #06b6d4;
                    box-shadow: 0 0 10px rgba(6, 182, 212, 0.8), 0 0 20px rgba(6, 182, 212, 0.4);
                }

                .genmap-bg-container .video-frame {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    width: 160px;
                    height: 90px;
                    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
                    border: 2px solid rgba(139, 92, 246, 0.5);
                    border-radius: 8px;
                    box-shadow: 0 4px 20px rgba(139, 92, 246, 0.3);
                    opacity: 0;
                    will-change: transform, opacity;
                }

                .genmap-bg-container .video-frame::before {
                    content: '';
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    width: 0;
                    height: 0;
                    border-style: solid;
                    border-width: 10px 0 10px 16px;
                    border-color: transparent transparent transparent rgba(139, 92, 246, 0.7);
                }

                .genmap-bg-container .youtube-layer {
                    opacity: 0;
                    animation: fadeInYoutube 2s ease-out 3s forwards;
                }

                @keyframes fadeInYoutube {
                    from {
                        opacity: 0;
                        transform: translateY(-20px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                .genmap-bg-container .youtube-player {
                    background: #282828;
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5);
                    transition: all 0.5s ease;
                    position: relative;
                }

                /* Platform-specific player styling */
                /* Desktop - Monitor screen */
                .genmap-bg-container .youtube-layer[data-platform="desktop"] .youtube-player {
                    background: #1a1a1a;
                    border-radius: 8px;
                    padding: 0;
                    box-shadow: 
                        0 8px 30px rgba(0, 0, 0, 0.5),
                        inset 0 0 0 1px rgba(255, 255, 255, 0.1);
                    border: 2px solid rgba(60, 60, 60, 0.5);
                    position: relative;
                }

                /* Desktop monitor stand/base */
                .genmap-bg-container .youtube-layer[data-platform="desktop"] .youtube-player::after {
                    content: '';
                    position: absolute;
                    bottom: -20px;
                    left: 50%;
                    transform: translateX(-50%);
                    width: 200px;
                    height: 12px;
                    background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%);
                    border-radius: 4px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
                }

                /* Tablet - iPad-like */
                .genmap-bg-container .youtube-layer[data-platform="tablet"] .youtube-player {
                    background: linear-gradient(135deg, #4a4a4a 0%, #3a3a3a 100%) !important;
                    border-radius: 24px !important;
                    padding: 12px !important;
                    box-shadow: 
                        0 12px 40px rgba(0, 0, 0, 0.9), 
                        inset 0 0 0 5px rgba(255, 255, 255, 0.25),
                        inset 0 0 0 10px rgba(0, 0, 0, 0.6),
                        0 0 0 3px rgba(60, 60, 60, 0.8);
                    border: 3px solid rgba(100, 100, 100, 0.3);
                    transform: scale(0.95);
                    transition: all 0.5s ease;
                }

                /* Mobile - Portrait phone - Position in middle right, below text box */
                .genmap-bg-container .youtube-layer[data-platform="mobile"] {
                    top: 50% !important;
                    bottom: auto !important;
                    right: 5% !important;
                    width: 90% !important;
                    max-width: 360px !important;
                    transform: translateY(-50%) !important;
                }

                .genmap-bg-container .youtube-layer[data-platform="mobile"] .youtube-player {
                    background: linear-gradient(135deg, #4a4a4a 0%, #3a3a3a 100%) !important;
                    border-radius: 40px !important;
                    padding: 16px 8px !important;
                    box-shadow: 
                        0 4px 15px rgba(0, 0, 0, 0.4), 
                        inset 0 0 0 6px rgba(255, 255, 255, 0.3),
                        inset 0 0 0 12px rgba(0, 0, 0, 0.7),
                        0 0 0 4px rgba(80, 80, 80, 0.9) !important;
                    border: 4px solid rgba(150, 150, 150, 0.4) !important;
                    transform: scale(0.85) !important;
                    max-width: 360px !important;
                    margin: 0 auto;
                    transition: all 0.5s ease;
                }

                /* Tablet Frame - iPad-like */
                .genmap-bg-container .mobile-phone-frame {
                    position: absolute;
                    top: -12px;
                    left: -12px;
                    right: -12px;
                    bottom: -12px;
                    pointer-events: none;
                    z-index: 15;
                    opacity: 0;
                    transition: opacity 0.5s ease;
                    border: 6px solid rgba(50, 50, 50, 0.95);
                    border-radius: 28px;
                    background: linear-gradient(135deg, rgba(60, 60, 60, 0.9) 0%, rgba(40, 40, 40, 0.9) 100%);
                    box-shadow: 
                        inset 0 0 0 2px rgba(255, 255, 255, 0.2),
                        0 0 0 2px rgba(0, 0, 0, 0.6),
                        0 8px 20px rgba(0, 0, 0, 0.8);
                }

                .genmap-bg-container .youtube-layer[data-platform="tablet"] .mobile-phone-frame {
                    opacity: 1;
                }

                /* Mobile Phone Frame - Portrait phone */
                .genmap-bg-container .youtube-layer[data-platform="mobile"] .mobile-phone-frame {
                    opacity: 1;
                    border: 8px solid rgba(50, 50, 50, 0.95);
                    border-radius: 44px;
                    top: -16px;
                    left: -8px;
                    right: -8px;
                    bottom: -16px;
                }

                /* Mobile phone notch */
                .genmap-bg-container .youtube-layer[data-platform="mobile"] .mobile-phone-frame::before {
                    content: '';
                    position: absolute;
                    top: -8px;
                    left: 50%;
                    transform: translateX(-50%);
                    width: 120px;
                    height: 24px;
                    background: linear-gradient(135deg, rgba(50, 50, 50, 0.98) 0%, rgba(40, 40, 40, 0.98) 100%);
                    border-radius: 0 0 18px 18px;
                    z-index: 16;
                    border: 2px solid rgba(0, 0, 0, 0.3);
                    border-top: none;
                }

                .genmap-bg-container .mobile-status-bar {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    height: 24px;
                    background: rgba(0, 0, 0, 0.7);
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 0 12px;
                    font-size: 10px;
                    color: rgba(255, 255, 255, 0.95);
                    font-weight: 600;
                    z-index: 11;
                    backdrop-filter: blur(10px);
                    opacity: 0;
                    transition: opacity 0.5s ease;
                }

                .genmap-bg-container .youtube-layer[data-platform="tablet"] .mobile-status-bar,
                .genmap-bg-container .youtube-layer[data-platform="mobile"] .mobile-status-bar {
                    opacity: 1;
                }

                .genmap-bg-container .mobile-time {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                    letter-spacing: 0.5px;
                }

                .genmap-bg-container .mobile-status-icons {
                    display: flex;
                    gap: 4px;
                    align-items: center;
                    font-size: 9px;
                }

                .genmap-bg-container .player-screen {
                    position: relative;
                    width: 100%;
                    padding-top: 56.25%;
                    background: #000;
                    overflow: hidden;
                }

                /* Mobile portrait aspect ratio */
                .genmap-bg-container .youtube-layer[data-platform="mobile"] .player-screen {
                    padding-top: 177.78%; /* 9:16 aspect ratio for portrait phone */
                }

                .genmap-bg-container .player-screen::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: linear-gradient(135deg, #1e1e1e 0%, #0a0a0a 100%);
                    opacity: 0.8;
                    transition: background 0.5s ease;
                }

                /* Platform-specific screen backgrounds */
                .genmap-bg-container .youtube-layer[data-platform="desktop"] .player-screen::before {
                    background: linear-gradient(135deg, #1e1e1e 0%, #0a0a0a 100%);
                }

                .genmap-bg-container .youtube-layer[data-platform="tablet"] .player-screen {
                    border-radius: 16px;
                    overflow: hidden;
                }

                .genmap-bg-container .youtube-layer[data-platform="tablet"] .player-screen::before {
                    background: linear-gradient(135deg, #0a0a0a 0%, #000000 100%);
                    border-radius: 16px;
                }

                .genmap-bg-container .youtube-layer[data-platform="mobile"] .player-screen {
                    border-radius: 24px;
                    overflow: hidden;
                    margin-top: 0;
                }

                .genmap-bg-container .youtube-layer[data-platform="mobile"] .player-screen::before {
                    background: linear-gradient(135deg, #0a0a0a 0%, #000000 100%);
                    border-radius: 24px;
                }

                .genmap-bg-container .gear-image-container {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    z-index: 100;
                    pointer-events: none;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .genmap-bg-container .gear-image {
                    width: 160px;
                    height: 160px;
                    animation: rotateGear 3s linear infinite;
                    opacity: 0.9;
                    position: relative;
                    z-index: 101;
                }

                @keyframes rotateGear {
                    from {
                        transform: rotate(0deg);
                    }
                    to {
                        transform: rotate(360deg);
                    }
                }


                .genmap-bg-container .player-controls {
                    background: #181818;
                    padding: 8px 12px;
                    transition: background 0.5s ease;
                }

                .genmap-bg-container .youtube-layer[data-platform="desktop"] .player-controls {
                    background: #181818;
                }

                .genmap-bg-container .youtube-layer[data-platform="tablet"] .player-controls {
                    background: #0a0a0a;
                    border-radius: 0 0 16px 16px;
                }

                .genmap-bg-container .youtube-layer[data-platform="mobile"] .player-controls {
                    background: #0a0a0a;
                    border-radius: 0 0 24px 24px;
                    margin-top: -1px;
                }

                .genmap-bg-container .progress-bar {
                    width: 100%;
                    height: 3px;
                    background: rgba(255, 255, 255, 0.2);
                    border-radius: 2px;
                    margin-bottom: 8px;
                    position: relative;
                    overflow: hidden;
                }

                .genmap-bg-container .progress-fill {
                    height: 100%;
                    background: #ff0000;
                    width: 0%;
                    border-radius: 2px;
                    animation: progressFill 8s linear infinite;
                    transition: background 0.5s ease;
                }

                /* Platform-specific progress colors */
                .genmap-bg-container .youtube-layer[data-platform="desktop"] .progress-fill {
                    background: #ff0000;
                }

                .genmap-bg-container .youtube-layer[data-platform="tablet"] .progress-fill {
                    background: #007aff;
                }

                .genmap-bg-container .youtube-layer[data-platform="mobile"] .progress-fill {
                    background: #007aff;
                }

                @keyframes progressFill {
                    0% { width: 0%; }
                    100% { width: 100%; }
                }

                .genmap-bg-container .control-buttons {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    color: #fff;
                    font-size: 12px;
                }

                .genmap-bg-container .control-icon {
                    width: 24px;
                    height: 24px;
                    background: rgba(255, 255, 255, 0.1);
                    border-radius: 4px;
                }

                .genmap-bg-container .time-display {
                    color: rgba(255, 255, 255, 0.7);
                    font-size: 12px;
                }

                .genmap-bg-container .right-controls {
                    display: flex;
                    gap: 8px;
                    margin-left: auto;
                }

                @media (max-width: 768px) {
                    .genmap-bg-container .text-prompt {
                        font-size: 14px;
                        padding: 15px 20px;
                    }
                    
                    .genmap-bg-container .video-frame {
                        width: 120px;
                        height: 68px;
                    }

                    .genmap-bg-container .gear-image {
                        width: 120px;
                        height: 120px;
                    }
                }

                @media (max-width: 480px) {
                    .genmap-bg-container .text-prompt {
                        font-size: 11px;
                        padding: 10px 14px;
                        min-height: 50px;
                    }
                    
                    .genmap-bg-container .video-frame {
                        width: 90px;
                        height: 51px;
                    }
                    
                    .genmap-bg-container .control-icon {
                        width: 20px;
                        height: 20px;
                    }

                    .genmap-bg-container .gear-image {
                        width: 90px;
                        height: 90px;
                    }
                }
            `}),e.jsxs("div",{className:"genmap-bg-container w-full h-full",children:[e.jsx("div",{className:"absolute",style:{bottom:"calc(19% - 15px)",right:"5%",width:"80%",maxWidth:"700px",zIndex:10},children:e.jsx("div",{ref:l,className:"text-prompt"})}),e.jsx("div",{ref:r,className:"absolute inset-0",style:{zIndex:5}}),e.jsx("div",{className:"absolute",style:{top:"48%",right:"5%",transform:"translateY(-50%)",width:"600px",height:"340px",zIndex:7},children:e.jsx("div",{ref:g,className:"relative w-full h-full"})}),e.jsx("div",{ref:u,className:"youtube-layer absolute","data-platform":"desktop",style:{top:"8%",right:"5%",width:"90%",maxWidth:"640px",zIndex:10},children:e.jsxs("div",{className:"youtube-player",children:[e.jsx("div",{className:"mobile-phone-frame"}),e.jsxs("div",{ref:s,className:"player-screen",children:[e.jsxs("div",{className:"mobile-status-bar",children:[e.jsx("span",{className:"mobile-time",children:"9:41"}),e.jsxs("div",{className:"mobile-status-icons",children:[e.jsx("span",{className:"mobile-signal",children:"📶"}),e.jsx("span",{className:"mobile-wifi",children:"📶"}),e.jsx("span",{className:"mobile-battery",children:"🔋"})]})]}),e.jsx("div",{className:"gear-image-container",children:e.jsx("img",{src:de("gear.png"),alt:"Spinning gear",className:"gear-image"})})]}),e.jsxs("div",{className:"player-controls",children:[e.jsx("div",{className:"progress-bar",children:e.jsx("div",{className:"progress-fill"})}),e.jsxs("div",{className:"control-buttons",children:[e.jsx("div",{className:"control-icon"}),e.jsx("div",{className:"time-display",children:"0:00 / 2:45"}),e.jsxs("div",{className:"right-controls",children:[e.jsx("div",{className:"control-icon"}),e.jsx("div",{className:"control-icon"}),e.jsx("div",{className:"control-icon"})]})]})]})]})})]})]})}function je({title:x,subtitle:l,description:r,ctaText:g,ctaLink:s="/articles"}){const{t:u}=O(),i=g||u("hero_cta_explore_resources");return e.jsxs("section",{className:"relative overflow-hidden bg-gradient-to-br from-secondary-900 via-secondary-800 to-secondary-700 text-white",children:[e.jsx(ve,{}),e.jsx("div",{className:"container-custom py-24 md:py-40 relative z-10",children:e.jsxs("div",{className:"max-w-4xl",children:[e.jsx("p",{className:"text-accent-500 font-semibold mb-4 text-lg",children:l}),e.jsx("h1",{className:"text-4xl md:text-6xl font-bold mb-6 leading-tight",children:x}),r&&e.jsx("p",{className:"text-xl text-secondary-100 mb-8 leading-relaxed max-w-3xl",children:r}),e.jsxs("div",{className:"flex flex-wrap gap-4",children:[e.jsx(k,{to:s,className:"inline-flex items-center justify-center px-8 py-4 border-2 border-white text-base font-semibold rounded-lg text-white bg-transparent hover:bg-white hover:text-secondary-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-200",children:i}),e.jsx("a",{href:"https://ai.kingdom.training/about/",className:"inline-flex items-center justify-center px-8 py-4 border-2 border-white/30 text-base font-semibold rounded-lg text-white hover:border-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-200",children:u("hero_cta_about_us")})]})]})}),e.jsx("div",{className:"relative h-0 bg-white",children:e.jsx("svg",{className:"absolute top-0 w-full h-16 -mt-16",preserveAspectRatio:"none",viewBox:"0 0 1440 54",fill:"none",children:e.jsx("path",{fill:"currentColor",className:"text-white",d:"M0 32L120 37.3C240 43 480 53 720 53.3C960 53 1200 43 1320 37.3L1440 32V54H1320C1200 54 960 54 720 54C480 54 240 54 120 54H0V32Z"})})})]})}function _e({variant:x="inline",title:l,description:r,showEmailInput:g=!1,className:s="",whiteBackground:u=!1,noWrapper:i=!1}){const{t:a}=O(),[c,v]=m.useState(""),[j,I]=m.useState(!0),N=m.useRef(null);m.useEffect(()=>{if(!g){I(!1);return}async function T(){try{const h=await me('[go_display_opt_in source="kt_news" name="Kingdom.Training"]');v(h)}catch(h){console.error("Error fetching shortcode:",h)}finally{I(!1)}}T()},[g]),m.useEffect(()=>{var p,b;if(!c||j||!g)return;const T=(p=N.current)==null?void 0:p.querySelector("form"),h=(b=N.current)==null?void 0:b.querySelector("#go-submit-form-button");if(!T||!h)return;const d=T,y=h;window.cf_token=null;const A="0x4AAAAAAA1dT7LSth0AgFDm",B=()=>new Promise(n=>{if(typeof window.turnstile<"u"){console.log("Turnstile API already loaded"),n();return}document.querySelectorAll('script[src*="challenges.cloudflare.com/turnstile"]').forEach(_=>{typeof window.turnstile>"u"&&_.remove()});const o=document.createElement("script");o.src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit",o.async=!0,o.onload=()=>{console.log("Turnstile script loaded successfully"),n()},o.onerror=_=>{console.error("Failed to load Turnstile script:",_),n()},document.head.appendChild(o)}),Y=async()=>{if(console.log("Initializing Turnstile widget..."),await B(),typeof window.turnstile>"u"){console.error("Turnstile API not available after script load");return}let n=d.querySelector(".cf-turnstile");if(!n){console.log("Creating Turnstile widget container"),n=document.createElement("div"),n.className="cf-turnstile",n.id="turnstile-widget";const o=d.querySelector("#go-submit-form-button");o&&o.parentNode?o.parentNode.insertBefore(n,o):d.appendChild(n)}if(n.setAttribute("data-sitekey",A),n.setAttribute("data-theme","light"),n.querySelector("iframe")){console.log("Turnstile widget already rendered");return}n.innerHTML="";try{console.log("Rendering Turnstile widget with site key:",A);const o=window.turnstile.render(n,{sitekey:A,theme:"light",callback:_=>{console.log("Turnstile token received"),window.cf_token=_,typeof window.save_cf=="function"&&window.save_cf(_)},"error-callback":()=>{console.error("Turnstile widget error")},"expired-callback":()=>{console.log("Turnstile token expired"),window.cf_token=null}});console.log("Turnstile widget rendered, ID:",o)}catch(o){console.error("Error rendering Turnstile widget:",o)}},q=()=>{const n=d.querySelector(".cf-turnstile");if(!(n==null?void 0:n.getAttribute("data-sitekey"))){console.warn("Cloudflare Turnstile site key not configured");return}return typeof window.turnstile>"u"?(console.log("Waiting for Cloudflare Turnstile script to load..."),!1):(n==null?void 0:n.querySelector("iframe"))?!0:(console.log("Waiting for Cloudflare Turnstile widget to render..."),!1)};window.save_cf=function(n){console.log("save_cf called with token"),window.cf_token=n};const w=setTimeout(async()=>{await Y(),setTimeout(()=>{q()||(console.warn("Turnstile widget may not have rendered properly. Retrying..."),Y())},2e3)},200),F=()=>{var f;const n=(f=N.current)==null?void 0:f.querySelectorAll("script");if(!n)return null;for(const o of Array.from(n)){const S=(o.textContent||"").match(/X-WP-Nonce['"]:\s*['"]([^'"]+)['"]/);if(S)return S[1]}return null},z=()=>window.cf_token||null,t=async n=>{var f,o,_,S,$;n.preventDefault(),n.stopPropagation();try{const L=((f=d.querySelector('input[name="email2"]'))==null?void 0:f.value)||"",H=((o=d.querySelector('input[name="email"]'))==null?void 0:o.value)||"",ae=((_=d.querySelector('input[name="first_name"]'))==null?void 0:_.value)||"",oe=((S=d.querySelector('input[name="last_name"]'))==null?void 0:S.value)||"",se=($=d.querySelector("#confirm-subscribe"))==null?void 0:$.checked;if(H)return;if(!se){const R=d.querySelector(".dt-form-error");R&&(R.textContent=a("newsletter_confirm_subscribe"),R.style.display="block");return}const X=F(),G=z();if(!X)throw console.error("WordPress nonce not found"),new Error(a("error_security_token_not_found"));const P=d.querySelector(".cf-turnstile"),U=P==null?void 0:P.getAttribute("data-sitekey");if(U&&!G){const R=d.querySelector(".dt-form-error");R&&(P&&P.offsetHeight>0&&P.offsetWidth>0?R.textContent=a("newsletter_security_complete"):R.textContent=a("newsletter_security_loading"),R.style.display="block",P&&P.scrollIntoView({behavior:"smooth",block:"center"}));return}U||console.warn("Cloudflare Turnstile site key not configured. Form submission may fail on the server.");const V=d.getAttribute("action")||"/wp-json/go-webform/double-optin",ie=V.startsWith("http")?V:`${window.location.origin}${V}`;y.setAttribute("disabled","disabled");const J=y.textContent;y.textContent=a("ui_submitting");const D=d.querySelector(".dt-form-error");D&&(D.style.display="none");const Z=await fetch(ie,{method:"POST",headers:{"Content-Type":"application/json","X-WP-Nonce":X},body:JSON.stringify({email:L,first_name:ae,last_name:oe,source:"kt_news",cf_turnstile:G})}),W=await Z.json();if(Z.ok&&W!==!1){y.textContent=a("newsletter_subscribed"),y.classList.add("bg-green-500"),d.reset();const R=d.querySelector(".dt-form-success");R&&(R.textContent=a("newsletter_check_email"),R.style.display="block"),setTimeout(()=>{y.removeAttribute("disabled"),y.textContent=J,y.classList.remove("bg-green-500")},3e3)}else y.removeAttribute("disabled"),y.textContent=J||a("newsletter_try_again"),D&&(D.textContent=(W==null?void 0:W.message)||a("error_subscribe_failed"),D.style.display="block")}catch(L){console.error("Error submitting form:",L),y.removeAttribute("disabled"),y.textContent=a("newsletter_try_again");const H=d.querySelector(".dt-form-error");H&&(H.textContent=a("error_subscribe_failed"),H.style.display="block")}};return d.addEventListener("submit",t),()=>{clearTimeout(w),d.removeEventListener("submit",t)}},[c,j,g]);const E=l||a("newsletter_stay_connected"),C=r||a("newsletter_default_description");if(x==="banner"){const T=u?"bg-white text-gray-900":"bg-gradient-to-r from-primary-800 to-primary-600 text-white",h=u?"text-gray-900":"",d=u?"text-gray-700":"text-primary-100",y=u?"text-primary-600":"text-accent-500",A=e.jsx("div",{className:`${i?"":T} ${i?"":"py-12"} ${s}`,children:e.jsx("div",{className:"container-custom",children:e.jsxs("div",{className:"max-w-4xl mx-auto text-center",children:[e.jsx(ee,{className:`w-12 h-12 mx-auto mb-4 ${y}`}),e.jsx("h2",{className:`text-3xl font-bold mb-4 ${h}`,children:E}),e.jsx("p",{className:`text-xl mb-8 max-w-2xl mx-auto ${d}`,children:C}),g?e.jsx("div",{className:"max-w-md mx-auto",ref:N,children:j?e.jsx("div",{className:"flex items-center justify-center py-4",children:e.jsx("div",{className:"w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"})}):c?e.jsx("div",{dangerouslySetInnerHTML:{__html:c}}):null}):e.jsxs(k,{to:"/newsletter",className:`inline-flex items-center justify-center px-8 py-4 font-semibold rounded-lg transition-colors duration-200 text-lg ${u?"bg-primary-600 hover:bg-primary-700 text-white":"bg-accent-600 hover:bg-accent-500 text-secondary-900"}`,children:[a("nav_subscribe_newsletter"),e.jsx(K,{className:"w-5 h-5 ml-2"})]})]})})});return i?A:e.jsx("section",{children:A})}return x==="card"?e.jsx("div",{className:`bg-background-50 border-2 border-primary-200 rounded-lg p-6 md:p-8 ${s}`,children:e.jsxs("div",{className:"flex items-start gap-4",children:[e.jsx("div",{className:"flex-shrink-0 w-12 h-12 bg-primary-500 rounded-full flex items-center justify-center",children:e.jsx(ee,{className:"w-6 h-6 text-white"})}),e.jsxs("div",{className:"flex-1",children:[e.jsx("h3",{className:"text-xl font-bold text-gray-900 mb-2",children:E}),e.jsx("p",{className:"text-gray-700 mb-4",children:C}),g?e.jsx("div",{ref:N,children:j?e.jsx("div",{className:"flex items-center justify-center py-4",children:e.jsx("div",{className:"w-5 h-5 border-2 border-primary-500 border-t-transparent rounded-full animate-spin"})}):c?e.jsx("div",{dangerouslySetInnerHTML:{__html:c}}):null}):e.jsxs(k,{to:"/newsletter",className:"inline-flex items-center text-primary-500 hover:text-primary-600 font-semibold",children:[a("nav_subscribe_now"),e.jsx(K,{className:"w-4 h-4 ml-1"})]})]})]})}):e.jsxs("div",{className:`flex flex-col sm:flex-row items-center justify-between gap-4 p-6 bg-primary-50 rounded-lg ${s}`,children:[e.jsxs("div",{className:"flex-1",children:[e.jsx("h3",{className:"text-lg font-semibold text-gray-900 mb-1",children:E}),e.jsx("p",{className:"text-sm text-gray-700",children:C})]}),g?e.jsx("div",{className:"w-full sm:w-auto",ref:N,children:j?e.jsx("div",{className:"flex items-center justify-center py-2",children:e.jsx("div",{className:"w-5 h-5 border-2 border-primary-500 border-t-transparent rounded-full animate-spin"})}):c?e.jsx("div",{dangerouslySetInnerHTML:{__html:c}}):null}):e.jsxs(k,{to:"/newsletter",className:"inline-flex items-center px-6 py-2 bg-primary-500 hover:bg-primary-600 text-white font-semibold rounded-lg transition-colors whitespace-nowrap",children:[a("newsletter_subscribe"),e.jsx(K,{className:"w-4 h-4 ml-2"})]})]})}function Ne({title:x="Key Information",items:l,className:r=""}){return l.length===0?null:e.jsx("section",{className:`py-12 bg-gray-50 border-t border-gray-200 ${r}`,children:e.jsx("div",{className:"container-custom",children:e.jsxs("div",{className:"max-w-4xl mx-auto",children:[e.jsx("h2",{className:"text-2xl font-bold text-gray-900 mb-6",children:x}),e.jsx("dl",{className:"space-y-4",children:l.map((g,s)=>e.jsxs("div",{className:"bg-white p-4 rounded-lg shadow-sm",children:[e.jsx("dt",{className:"text-lg font-semibold text-gray-900 mb-2",children:g.term}),e.jsx("dd",{className:"text-gray-700 leading-relaxed",children:g.definition})]},s))})]})})})}function ke({articles:x}){const l=m.useRef(null),r=m.useRef(null),g=m.useRef(null);if(m.useEffect(()=>{l.current&&(g.current=l.current.closest("section"));const a=()=>{if(!l.current&&!r.current||!g.current)return;const j=window.scrollY*.15;l.current&&(l.current.style.transform=`translateY(${j}px)`),r.current&&(r.current.style.transform=`translateY(${j}px)`)};return window.addEventListener("scroll",a,{passive:!0}),a(),()=>{window.removeEventListener("scroll",a)}},[]),!x||x.length===0)return null;const s=a=>{const c=document.createElement("DIV");return c.innerHTML=a,c.textContent||c.innerText||""},u=a=>{const c=s(a);return c.length>36?c.substring(0,36).trim()+"...":c},i={fontSize:"clamp(1.4rem, 2vw, 1.8rem)",lineHeight:"1.2",color:"rgba(107, 114, 128, 0.15)",fontWeight:500,fontFamily:"'Courier New', Courier, 'Lucida Console', Monaco, monospace",maxHeight:"2.4em",overflow:"hidden",overflowWrap:"break-word",filter:"blur(1px)",textShadow:"0 0 3px rgba(107, 114, 128, 0.15)"};return e.jsxs(e.Fragment,{children:[e.jsxs("div",{ref:l,className:"absolute left-0 w-1/2 pointer-events-none overflow-hidden",style:{top:"-15%",bottom:"-15%",height:"130%",willChange:"transform",zIndex:0},children:[e.jsx("div",{className:"absolute inset-0 pointer-events-none",style:{background:"linear-gradient(to left, white 0%, rgba(255, 255, 255, 0.8) 20%, rgba(255, 255, 255, 0.4) 40%, transparent 60%)",zIndex:1}}),e.jsx("div",{className:"h-full flex flex-col justify-start items-start pl-4 md:pl-8 lg:pl-16 pt-12 relative",style:{zIndex:0},children:x.map(a=>e.jsx("div",{className:"text-left mb-1 md:mb-1.5 max-w-[85%]",style:i,children:u(a.title.rendered)},`left-${a.id}`))})]}),e.jsxs("div",{ref:r,className:"absolute right-0 w-1/2 pointer-events-none overflow-hidden",style:{top:"-15%",bottom:"-15%",height:"130%",willChange:"transform",zIndex:0},children:[e.jsx("div",{className:"absolute inset-0 pointer-events-none",style:{background:"linear-gradient(to right, white 0%, rgba(255, 255, 255, 0.8) 20%, rgba(255, 255, 255, 0.4) 40%, transparent 60%)",zIndex:1}}),e.jsx("div",{className:"h-full flex flex-col justify-start items-end pr-4 md:pr-8 lg:pr-16 pt-12 relative",style:{zIndex:0},children:x.map(a=>e.jsx("div",{className:"text-right mb-1 md:mb-1.5 max-w-[85%]",style:i,children:u(a.title.rendered)},`right-${a.id}`))})]})]})}function qe(){const{lang:x}=le(),l=ce(),{t:r,tWithReplace:g}=O(),{defaultLang:s,loading:u}=ue(),i=x||pe(l.pathname).lang||void 0,c=!!i||!u,v=i||s||null,{data:j=[],isLoading:I}=te({per_page:3,orderby:"date",order:"desc",lang:v||void 0,enabled:c}),{data:N=[],isLoading:E}=te({per_page:15,orderby:"date",order:"desc",lang:v||void 0,enabled:c}),{data:C=[],isLoading:T}=be({per_page:3,orderby:"date",order:"desc",lang:v||void 0,enabled:c}),{data:h=[],isLoading:d}=ye(i,s,c),y=m.useMemo(()=>re(j,v),[j,v]),A=m.useMemo(()=>re(N,v),[N,v]),B=m.useMemo(()=>he(C,v),[C,v]);if(!c||I||E||T||d)return e.jsx("div",{className:"flex items-center justify-center min-h-[400px]",children:e.jsxs("div",{className:"text-center",children:[e.jsx("div",{className:"inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary-500"}),e.jsx("p",{className:"mt-4 text-gray-600",children:r("ui_loading")})]})});const q=typeof window<"u"?window.location.origin:"https://ai.kingdom.training";return e.jsxs(e.Fragment,{children:[e.jsx(fe,{title:r("page_home"),description:r("seo_home_description"),keywords:"disciple making movements, media to movements, M2DMM, digital discipleship, online evangelism, church planting, unreached peoples, kingdom training, strategy course, MVP course"}),e.jsx(ge,{website:{name:"Kingdom.Training",url:q,description:r("footer_mission_statement")}}),e.jsx(je,{subtitle:r("hero_subtitle_media_ai"),title:r("hero_title_innovate"),description:r("hero_description"),ctaText:r("nav_start_mvp"),ctaLink:M("/strategy-courses",i||null,s)}),e.jsxs("section",{className:"relative py-12 bg-white overflow-hidden",children:[e.jsx(ke,{articles:A}),e.jsx("div",{className:"relative z-10",children:e.jsx(_e,{variant:"banner",title:r("hero_newsletter_title"),description:r("home_newsletter_description"),showEmailInput:!1,className:"my-0",whiteBackground:!0,noWrapper:!0})})]}),e.jsxs("section",{className:"relative py-20 bg-gradient-to-br from-secondary-900 to-secondary-700 text-white overflow-hidden",children:[e.jsx(xe,{}),e.jsx("div",{className:"container-custom relative z-10",children:e.jsxs("div",{className:"max-w-4xl mx-auto text-center",children:[e.jsx("h2",{className:"text-3xl md:text-5xl font-bold mb-4",children:r("page_mvp_strategy_course")}),e.jsx("p",{className:"text-xl text-secondary-100 mb-8 max-w-2xl mx-auto",children:r("home_mvp_description")}),e.jsxs("div",{className:"bg-white/10 backdrop-blur-sm rounded-lg p-8 mb-8 text-left",children:[e.jsx("h3",{className:"text-xl font-semibold mb-4 text-accent-500",children:g("page_step_curriculum",{count:h.length>0?h.length:10})}),h.length>0?e.jsxs("div",{className:"grid md:grid-cols-2 gap-4 text-sm",children:[e.jsx("div",{className:"flex flex-col gap-4",children:h.slice(0,Math.ceil(h.length/2)).map((w,F)=>e.jsxs(k,{to:M(`/strategy-courses/${w.slug}`,i||null,s),className:"hover:text-accent-400 transition-colors",children:[w.steps||F+1,". ",w.title.rendered]},w.id))}),e.jsx("div",{className:"flex flex-col gap-4",children:h.slice(Math.ceil(h.length/2)).map((w,F)=>{const z=w.steps||Math.ceil(h.length/2)+F+1;return e.jsxs(k,{to:M(`/strategy-courses/${w.slug}`,i||null,s),className:"hover:text-accent-400 transition-colors",children:[z,". ",w.title.rendered]},w.id)})})]}):e.jsx("p",{className:"text-secondary-200",children:r("home_loading_steps")})]}),e.jsx(k,{to:M("/strategy-courses",i||null,s),className:"inline-flex items-center justify-center px-8 py-4 bg-accent-600 hover:bg-accent-500 text-secondary-900 font-semibold rounded-lg transition-colors duration-200 text-lg",children:r("nav_enroll_mvp")})]})})]}),e.jsx("section",{className:"py-16 bg-background-50",children:e.jsxs("div",{className:"container-custom",children:[e.jsxs("div",{className:"flex items-center justify-between mb-8",children:[e.jsx("h2",{className:"text-3xl font-bold text-gray-800",children:r("page_latest_articles")}),e.jsxs(k,{to:M("/articles",i||null,s),className:"text-primary-500 hover:text-primary-600 font-medium",children:[r("ui_view_all")," →"]})]}),y.length>0?e.jsx("div",{className:"grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8",children:y.map(w=>e.jsx(Q,{post:w,type:"articles",lang:i||null,defaultLang:s},w.id))}):e.jsxs("div",{className:"text-center py-12 bg-white rounded-lg",children:[e.jsx("p",{className:"text-gray-600 mb-4",children:r("msg_no_articles")}),e.jsxs(k,{to:M("/articles",i||null,s),className:"text-primary-500 hover:text-primary-600 font-medium",children:[r("ui_browse_all")," ",r("nav_articles").toLowerCase()," →"]})]})]})}),e.jsx("section",{className:"py-16 bg-white",children:e.jsxs("div",{className:"container-custom",children:[e.jsxs("div",{className:"flex items-center justify-between mb-8",children:[e.jsx("h2",{className:"text-3xl font-bold text-gray-800",children:r("page_featured_tools")}),e.jsxs(k,{to:M("/tools",i||null,s),className:"text-primary-500 hover:text-primary-600 font-medium",children:[r("ui_view_all")," →"]})]}),B.length>0?e.jsx("div",{className:"grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8",children:B.map(w=>e.jsx(Q,{post:w,type:"tools",lang:i||null,defaultLang:s},w.id))}):e.jsxs("div",{className:"text-center py-12 bg-background-50 rounded-lg",children:[e.jsx("p",{className:"text-gray-600 mb-4",children:r("msg_no_tools")}),e.jsxs(k,{to:M("/tools",i||null,s),className:"text-primary-500 hover:text-primary-600 font-medium",children:[r("ui_browse_all")," ",r("nav_tools").toLowerCase()," →"]})]})]})}),e.jsx("section",{className:"py-20 bg-primary-800 text-white",children:e.jsx("div",{className:"container-custom",children:e.jsxs("div",{className:"max-w-4xl mx-auto text-center",children:[e.jsxs("div",{className:"mb-12",children:[e.jsx("h2",{className:"text-3xl md:text-4xl font-bold text-white text-center mb-8",children:r("content_digital_disciple_making")}),e.jsx("div",{className:"relative w-full",style:{paddingBottom:"56.25%"},children:e.jsx("iframe",{src:"https://player.vimeo.com/video/436776178?title=0&byline=0&portrait=0",className:"absolute top-0 left-0 w-full h-full rounded-lg shadow-2xl",frameBorder:"0",allow:"autoplay; fullscreen; picture-in-picture",allowFullScreen:!0,title:r("video_kingdom_training_title")})})]}),e.jsx("h2",{className:"text-3xl md:text-4xl font-bold mb-6",children:r("content_heavenly_economy")}),e.jsx("p",{className:"text-lg text-primary-100 leading-relaxed mb-6",children:r("home_heavenly_economy")}),e.jsx("p",{className:"text-lg text-primary-100 leading-relaxed mb-8",children:r("home_mission_statement")}),e.jsxs("div",{className:"flex flex-wrap justify-center gap-4",children:[e.jsx(k,{to:M("/strategy-courses",i||null,s),className:"inline-flex items-center justify-center px-8 py-4 bg-accent-600 hover:bg-accent-500 text-secondary-900 font-semibold rounded-lg transition-colors duration-200",children:r("page_start_strategy_course")}),e.jsx(k,{to:M("/articles",i||null,s),className:"inline-flex items-center justify-center px-8 py-4 border-2 border-white/30 hover:border-white text-white font-semibold rounded-lg transition-colors duration-200",children:r("ui_read_articles")}),e.jsx(k,{to:M("/tools",i||null,s),className:"inline-flex items-center justify-center px-8 py-4 border-2 border-white/30 hover:border-white text-white font-semibold rounded-lg transition-colors duration-200",children:r("ui_explore_tools")})]})]})})}),e.jsx(Ne,{title:r("content_key_information_m2dmm"),items:[{term:r("content_m2dmm_term"),definition:r("content_m2dmm_definition")},{term:r("content_digital_disciple_making_term"),definition:r("content_digital_disciple_making_definition")},{term:r("content_mvp_course_term"),definition:r("content_mvp_course_definition")},{term:r("content_ai_discipleship_term"),definition:r("content_ai_discipleship_definition")},{term:r("content_heavenly_economy_term"),definition:r("content_heavenly_economy_definition")},{term:r("content_kingdom_training_for_term"),definition:r("content_kingdom_training_for_definition")}]})]})}export{qe as default};
