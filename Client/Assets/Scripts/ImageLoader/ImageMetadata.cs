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
}
