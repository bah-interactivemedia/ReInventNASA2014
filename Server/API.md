# API

---

## Images

###GET Images

/images/getImages?limit=[**limit**]

- limit: int

---

## Annotations

###GET Annotations
/annotations/getImageAnnotations?image=[**image**]

- image: int

###POST Annotation
/annotations/annotateImage?image=[**image**]&annotationBlob=[**annotationBlob**]

- image: int
- annotationBlob: string