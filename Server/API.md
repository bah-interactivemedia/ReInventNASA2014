# API

---

## Images

###GET Images

/images/getImages?limit=[**limit**]&sort=[**all**]

- limit: int
- sort: string
	- all (default)
	- layered
	- blueberries
	- brightRocks

###GET Images with same location

/images/getSameLocationImages?image=[**image**]

- image: int

---

## Annotations

###GET Annotations
/annotations/getImageAnnotations?image=[**image**]

- image: int

###POST Annotation
/annotations/annotateImage?image=[**image**]&annotationBlob=[**annotationBlob**]&category=[**category**]

- image: int
- annotationBlob: string
- category
	- layered
	- blueberries
	- brightRocks