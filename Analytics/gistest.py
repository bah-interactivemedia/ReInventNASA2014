import urllib2
import json
import math
import decimal
import Image
import os

rlst = []
rlst2 = []
rlst3 = []
img = Image.new( 'RGB', (2000, 2000), "black")

debug = False
url = "https://msl-raws.s3.amazonaws.com/images/image_manifest.json"
response = urllib2.urlopen(url)
data = json.load(response)

ntotal = 0
ntotal2 = 0
lastsiteind = 0
lastdriveind = 0

basex = 0
basey = 0
xo = 0
yo = 0
zo = 0
lxo = 0
lyo = 0
lzo = 0
odo = 0

for i in data['sols']:
        sol = i['sol']
        url2 = i['url']
        n = i['num_images']
        nn = 0
        filename = "d%d.json" % sol
        if os.path.isfile(filename):
                print "##### sol:",i['sol'],"n:",i['num_images'],"url:",filename
                infile = open(filename, 'r')
                data2 = json.load(infile)
                infile.close
        else:
                print "##### sol:",i['sol'],"n:",i['num_images'],"url:",i['url']
                response2 = urllib2.urlopen(url2)
                data2 = json.load(response2)
                outfile = open(filename, 'wb')
                json.dump(data2, outfile)
                outfile.close

        rlst3.append((basex+xo, basey+yo))
        
        nnn = 0
        for j in data2['ccam_images']:
                for jj in j['images']:
                        nn+=1
                        nnn+=1
        nccam = nnn

        nnn = 0
        for j in data2['fcam_images']:
                for jj in j['images']:
                        nn+=1
                        nnn+=1
        nfcam = nnn

        nnn = 0
        for j in data2['rcam_images']:
                for jj in j['images']:
                        nn+=1
                        nnn+=1
        nrcam = nnn
        
        nnn = 0
        for j in data2['ncam_images']:
                loc =  j['location']

                siteind = loc['site_index']
                if siteind != lastsiteind:
                        basex += xo
                        basey += yo
                        lxo = 0
                        lyo = 0

                        print "new site:",siteind,"x",basex,"y",basey,"odo",round(odo,1)
                        img.save("%d.png" % siteind)
                        lastsiteind = siteind
                        rlst.append((basex, basey))

                xo, yo, zo = loc['rover_xyz']

                driveind = loc['drive_index']
                if driveind != lastdriveind:
                        #print "new drive:",driveind
                        rlst2.append((basex+xo, basey+yo))
                        lastdriveind = driveind

                if xo != lxo or yo != lyo:
                        dx = float(lxo - xo)
                        dy = float(lyo - yo)
                        d = math.hypot(dx, dy)
                        odo += float(d)
                        lxo = xo
                        lyo = yo

                px = int(round((basex + xo) / 10 + 1000))
                py = int(round((basey + yo) / 10 + 1000))
                img.putpixel((px,py), (100,100,100))


                for jj in j['images']:
                        nn+=1
                        nnn+=1
        nncam = nnn
        
        nnn = 0
        for j in data2['mastcam_left_images']:
                nn+=1
                nnn+=1
        nmastl = nnn

        nnn = 0
        for j in data2['mastcam_right_images']:
                nn+=1
                nnn+=1
        nmastr = nnn

        nnn = 0
        for j in data2['mahli_images']:
                nn+=1
                nnn+=1
        nmahli = nnn

        nnn = 0
        for j in data2['mardi_images']:
                nn+=1
                nnn+=1
        nmardi = nnn

        print "--- sol:",sol,"n:",n,"nn:",nn,"ccam",nccam,"fcam",nfcam,"rcam",nrcam,"ncam",nncam,"mastl",nmastl,"mastr",nmastr,"mahl$
        if debug:
                print ""

        ntotal += n
        ntotal2 += nn
        
        print ntotal
print ntotal2
print round(odo, 1)

for k in rlst2:
        ppx, ppy = k
        px = int(round((ppx) / 10 + 1000))
        py = int(round((ppy) / 10 + 1000))

        img.putpixel((px-1,py), (100,100,100))
        img.putpixel((px+1,py), (100,100,100))
        img.putpixel((px+0,py), (100,100,100))

        img.putpixel((px-1,py+1), (100,100,100))
        img.putpixel((px+1,py+1), (100,100,100))
        img.putpixel((px+0,py+1), (100,100,100))

        img.putpixel((px-1,py-1), (100,100,100))
        img.putpixel((px+1,py-1), (100,100,100))
        img.putpixel((px+0,py-1), (100,100,100))

for k in rlst2:
        ppx, ppy = k
        px = int(round((ppx) / 10 + 1000))
        py = int(round((ppy) / 10 + 1000))
        img.putpixel((px,py), (10,200,10))

for k in rlst3:
        ppx, ppy = k
        px = int(round((ppx) / 10 + 1000))
        py = int(round((ppy) / 10 + 1000))
        img.putpixel((px,py), (10,10,250))

for k in rlst:
        ppx, ppy = k
        px = int(round((ppx) / 10 + 1000))
        py = int(round((ppy) / 10 + 1000))
        img.putpixel((px,py), (200,10,10))

rot = img.rotate(90)
rot.save("pix.png")
