using UnityEngine;
using System.Collections;
using System.Collections.Generic;

public class ImageMetadata : MonoBehaviour {

	private Dictionary<string, object> metadata;

	// Use this for initialization
	void Start () {
	
	}

	public void SetMetaData(Dictionary<string, object> metadata) {
		this.metadata = metadata;
	}

	public string Id() {
		return (string)metadata ["id"];
	}
	
	public int Height() {
		return int.Parse ((string)metadata ["height"]);
	}
	
	public int Width() {
		return int.Parse ((string)metadata ["width"]);
	}

	public void SubmitTag(int tag) {
		StartCoroutine (_SubmitTag (tag));
	}

	IEnumerator _SubmitTag(int tag) {
		string category;
		if (tag == 0) {
			category = "blueberries";
		} else if (tag == 2) {
			category = "brightRocks";
		} else {
			print ("unsuppported tag");
			yield break;
		}
		string url = ImageLoader.ENDPOINT + "annotations/annotateImage?image=" + Id () + "&annotationBlob=&category=" + category;
		print (url);
		WWW req = new WWW (url);

		yield return req;
	}

	public void SubmitLine(Vector3 tlw, Vector3 brw) {
		Vector2 tl = TopLeftHit(tlw, brw);
		Vector2 br = BottomRightHit(tlw,brw);
		if (tl.x != -1 && br.x != -1) {
			StartCoroutine(_SubmitAnnotation(tl, br, "line", "layered"));
		}
	}

	public void SubmitRect(Vector3 tlw, Vector3 brw) {
		Vector2 tl = TopLeftHit(tlw, brw);
		Vector2 br = BottomRightHit(tlw,brw);
		if (tl.x != -1 && br.x != -1) {
			StartCoroutine(_SubmitAnnotation(tl, br, "rect", "brightRocks"));
		}
	}
	
	IEnumerator _SubmitAnnotation(Vector2 tl, Vector2 br, string type, string category) {
		string annotation = "&annotationBlob[]="+type;
		annotation += "&annotationBlob[]="+tl.x;
		annotation += "&annotationBlob[]="+tl.y;
		annotation += "&annotationBlob[]="+br.x;
		annotation += "&annotationBlob[]="+br.y;
		string url = ImageLoader.ENDPOINT + "annotations/annotateImage?image=" + Id () + annotation + "&category=" + category;
		print (url);
		WWW req = new WWW (url);
		
		yield return req;
		print (req.text);
	}


	public Vector2 TopLeftHit(Vector3 tlw, Vector3 brw) {
		Vector3 delta = (brw - tlw) / 30;
		for (int i=0; i<29; i++) {
			RaycastHit hit;
			Ray ray = new Ray(tlw, Vector3.forward * 10);
			if (Physics.Raycast (ray, out hit) && hit.transform == transform) {
				return new Vector2(hit.textureCoord.x * Width(), hit.textureCoord.y * Height());
			}
			tlw += delta;
		}

		print ("raycast failed!");
		return new Vector2(-1,-1);
	}

	
	public Vector2 BottomRightHit(Vector3 tlw, Vector3 brw) {
		Vector3 delta = (tlw - brw) / 30;
		for (int i=0; i<29; i++) {
			RaycastHit hit;
			Ray ray = new Ray(brw, Vector3.forward * 10);
			if (Physics.Raycast (ray, out hit) && hit.transform == transform) {
				return new Vector2(hit.textureCoord.x * Width(), hit.textureCoord.y * Height());
			}
			brw += delta;
		}
		
		print ("raycast failed!");
		return new Vector2(-1,-1);
	}


}
