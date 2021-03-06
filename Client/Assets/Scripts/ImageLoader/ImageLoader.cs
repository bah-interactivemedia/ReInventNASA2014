﻿using UnityEngine;
using System.Collections;
using System.Collections.Generic;

public class ImageLoader : MonoBehaviour {

	public const float ImageScale = 1/100f;
	public GameObject prefabImage;
	public const string ENDPOINT = "http://nasa-jpl.elasticbeanstalk.com/";

	// Use this for initialization
	void Start () {

	}
	
	// Update is called once per frame
	void Update () {
	
	}

	public GameObject CreateGameObject(Dictionary<string, object> metadata) {
		GameObject go = (GameObject)GameObject.Instantiate (prefabImage);
		ImageMetadata md = go.GetComponent<ImageMetadata>();
		md.SetMetaData(metadata);

		return go;
	}

	public WWW GetWWWForImage(string image) {
		var path = "file:///"+Application.streamingAssetsPath + "/TestImages/" + image;
		print (path);
		return new WWW (path);
	}

	public string GetRandomImage() {
		int index = Random.Range (0, TestImages.List.Length - 1);
		return TestImages.List[index];
	}

	public void SetTexture(GameObject go, WWW www) {
		Texture2D tex = www.texture;
		go.renderer.material.SetTexture ("_MainTex", tex);
		go.transform.localScale = new Vector3 (tex.width * ImageScale,tex.height * ImageScale,1);
	}

	public delegate void ImageLoaded(string imageUrl, Dictionary<string, object> metadata);
	public void LoadRandomImages (ImageLoaded imageCallback, int count) {
		StartCoroutine (FetchImageList(count, imageCallback, (int nLoaded) => {
			count -= nLoaded;
			if (count > 0) {
				LoadRandomImages (imageCallback, count);
			}
		}));
	}

	public delegate void LoadComplete(int count);
	IEnumerator FetchImageList(int count, ImageLoaded imageCallback, LoadComplete completed) {
		WWW req = new WWW (ENDPOINT + "images/getImages?limit="+count+"&sort=all");
		yield return req;
		List<object> response = (List<object>)MiniJSON.Json.Deserialize (req.text);
		foreach (Dictionary<string, object> obj in response) {
			imageCallback((string)obj["url"], obj);
			yield return null;
		}
		completed (response.Count);
	}
}
