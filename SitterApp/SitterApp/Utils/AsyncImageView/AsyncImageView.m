//
//  AsyncImageView.m
//  Canogle
//
//  Created by Tushar Sarkar on 10/19/11.
//  Copyright 2011 Sofmen Inc. All rights reserved.
//

#import "AsyncImageView.h"
#import "ImageCacheObject.h"
#import "ImageCache.h"
#import <QuartzCore/QuartzCore.h>
#import "Constants.h"
//#import "JSONKit.h"

//
// Key's are URL strings.
// Value's are ImageCacheObject's
//



static ImageCache *imageCache = nil;

@implementation AsyncImageView
@synthesize toucheEvents;
@synthesize isFromFeatureonNow;
@synthesize imageView;
- (id)initWithFrame:(CGRect)frame {
    if (self = [super initWithFrame:frame]) {
    }
    return self;
}


- (void)drawRect:(CGRect)rect {
    // Drawing code
    self.backgroundColor =[UIColor clearColor];
}


- (void)dealloc {
    [connection cancel];
//    [super dealloc];
}

- (void)touchesEnded:(NSSet *)touches withEvent:(UIEvent *)event {
	toucheEvents = touches;
    [delegate performSelector:operation withObject:self];
}

- (void)setDelegate:(id)aDelegate operation:(SEL)anOperation {
    delegate = aDelegate;
    operation = anOperation;
}

- (void)setImageForCurrentView:(UIImage*)image
{
    if (!imageView) {
        imageView = [[UIImageView alloc] init];
        imageView.image=image;
        imageView.frame = self.bounds;
        imageView.autoresizesSubviews = YES;
        [imageView setContentMode:UIViewContentModeScaleAspectFill];
        imageView.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleRightMargin | UIViewAutoresizingFlexibleHeight;
        imageView.autoresizingMask = YES;
        imageView.clipsToBounds = YES;
        imageView.backgroundColor = [UIColor clearColor];
        [self addSubview:imageView];
    }
    else
        imageView.image=image;
}


- (void)setImageForCurrentView{
    if (!imageView) {
        imageView = [[UIImageView alloc] init];
        if (self.isAddTag==NO) {
            [imageView setContentMode:UIViewContentModeScaleAspectFill];
            
        }
        klocalImageName = [[NSString alloc] init];
    }
}
-(void)loadImageFromURL:(NSURL*)url isBlur:(BOOL)isToBlur {
    isBlur=isToBlur;
    [self loadImageFromURL:url];
   
}
-(void)loadImageFromURL:(NSURL*)url {
    if (!imageView) {
        imageView = [[UIImageView alloc] init];
        if (self.isAddTag==NO) {
            [imageView setContentMode:UIViewContentModeScaleAspectFill];
            
        }

        klocalImageName = [[NSString alloc] init];
    }
    
    if (url == nil) {
        if (self.frame.size.width > 200) {
            klocalImageName = @"iTunesArtwork.png";
        }else{
            klocalImageName = @"icon.png";
        }
        imageView.image = [UIImage imageNamed:klocalImageName];
        [self addSubview:imageView];
        imageView.frame = self.bounds;
        [imageView setNeedsLayout];
        [self setNeedsLayout];
        return;
    }
    imageView.frame = CGRectMake(0, 0, 40, 40);
    imageView.autoresizesSubviews = YES;
    imageView.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleRightMargin | UIViewAutoresizingFlexibleHeight;
    imageView.autoresizingMask = YES;
    imageView.clipsToBounds = YES;
    //imageView.layer.cornerRadius = kThumbImageRadious;
    imageView.backgroundColor = [UIColor clearColor];
    if (connection != nil) {
        [connection cancel];
        connection = nil;
    }
    if (data != nil) {
        data = nil;
    }
    
    if (imageCache == nil) 
        imageCache = [[ImageCache alloc] initWithMaxSize:2*1024*1024];
    urlString = [[url absoluteString] copy];
    UIImage *cachedImage = [imageCache imageForKey:urlString];
    
    if (cachedImage != nil) {
        if ([[self subviews] count] > 0) {
            [[[self subviews] safeObjectAtIndex:0] removeFromSuperview];
        }
        [imageView setImage:cachedImage];
        if (self.tag==111)
        [[NSNotificationCenter defaultCenter]postNotificationName:@"ImageLoaded" object:nil];
        imageView.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleHeight;
        [self addSubview:imageView];
        
        
        imageView.frame = self.bounds;
        if (self.isVideo) {
            UIImage* image=[UIImage imageNamed:@"play"];
            self.iv=[[UIImageView alloc] initWithImage:image];
            self.iv.center=imageView.center;
            [self addSubview:self.iv];
            
        }
        [imageView setNeedsLayout];
        [self setNeedsLayout];
        return;
    }
    
#define SPINNY_TAG 5555   
	if ([[self subviews] count] > 0) {
		[[[self subviews] safeObjectAtIndex:0] removeFromSuperview];
	}
    if (self.hideLoading==NO) {
        UIActivityIndicatorView *spinny;
        spinny = [[UIActivityIndicatorView alloc] initWithActivityIndicatorStyle:UIActivityIndicatorViewStyleGray];
        spinny.tag = SPINNY_TAG;
        if (self.bounds.size.width>0) {
            self.imageView.frame = self.bounds;
        }
        spinny.center = CGPointMake(self.imageView.frame.size.width/2, self.imageView.frame.size.height/2);
        [spinny startAnimating];
        [self addSubview:spinny];
        
    }
    
    NSURLRequest *request = [NSURLRequest requestWithURL:url 
                                             cachePolicy:NSURLRequestUseProtocolCachePolicy 
                                         timeoutInterval:30];
    connection = [[NSURLConnection alloc] initWithRequest:request delegate:self];
}

- (void)connection:(NSURLConnection *)connection 
    didReceiveData:(NSData *)incrementalData {
    if (data==nil) {
        data = [[NSMutableData alloc] initWithCapacity:2048];
    }
    [data appendData:incrementalData];
}

- (void)connectionDidFinishLoading:(NSURLConnection *)aConnection {
    connection = nil;
    
    UIView *spinny = [self viewWithTag:SPINNY_TAG];
    [spinny removeFromSuperview];
    
    if ([[self subviews] count] > 0) {
        [[[self subviews] safeObjectAtIndex:0] removeFromSuperview];
    }
    if (self.frame.size.width > 200) {
       // klocalImageName = @"iTunesArtwork.png";//@"hdr_noImage.png";
    }else{
       // klocalImageName = @"icon.png";//@"thumb_noImage.png";
    }
    UIImage *image = [UIImage imageWithData:data];
    if (isBlur) {
        image=[self blur:image];
        isBlur=NO;
    }
    if([imageCache imageForKey:urlString] == nil)
	{
        if ([data length]>0) {
            [imageCache insertImage:image withSize:[data length] forKey:urlString]; 
        }else{
            NSData *imageData = UIImagePNGRepresentation([UIImage imageNamed:klocalImageName]);
            [imageCache insertImage:[UIImage imageNamed:klocalImageName] withSize:[imageData length] forKey:urlString];
            image = [UIImage imageNamed:klocalImageName];
        }
	}
    
    [imageView setImage:image];
    if (self.tag==111)
        [[NSNotificationCenter defaultCenter]postNotificationName:@"ImageLoaded" object:nil];
    imageView.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleHeight;
    [self addSubview:imageView];
    imageView.frame = self.bounds;
    if (self.isVideo) {
        UIImage* image=[UIImage imageNamed:@"play"];
        self.iv=[[UIImageView alloc] initWithImage:image];
        self.iv.center=imageView.center;
        [self addSubview:self.iv];
        
    }

	[imageView setNeedsLayout];
    [self setNeedsLayout];
    data = nil;
}


- (void)connection:(NSURLConnection *)aConnection didFailWithError:(NSError *)error{
    connection = nil;
    if (self.frame.size.width > 200) {
        //klocalImageName = @"iTunesArtwork.png";//@"hdr_noImage.png";
    }else{
       // klocalImageName = @"icon.png";//@"thumb_noImage.png";
    }
    UIView *spinny = [self viewWithTag:SPINNY_TAG];
    [spinny removeFromSuperview];
    
    if ([[self subviews] count] > 0) {
        [[[self subviews] safeObjectAtIndex:0] removeFromSuperview];
    }
    UIImage *image = nil;
    if([imageCache imageForKey:urlString] == nil)
	{         
        NSData *imageData = UIImagePNGRepresentation([UIImage imageNamed:klocalImageName]);
        [imageCache insertImage:[UIImage imageNamed:klocalImageName] withSize:[imageData length] forKey:urlString];
        [imageCache insertImage:image withSize:[imageData length] forKey:urlString];
    }else{
        
    }
    image = [UIImage imageNamed:klocalImageName];
    [imageView setImage:image];
    imageView.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleHeight;
    [self addSubview:imageView];
    imageView.frame = self.bounds;
	[imageView setNeedsLayout]; 
    [self setNeedsLayout];
}

+(void)clearCache
{
	imageCache = nil;
}
- (UIImage*) blur:(UIImage*)theImage
{
    // create our blurred image
    CIContext *context = [CIContext contextWithOptions:nil];
    CIImage *inputImage = [CIImage imageWithCGImage:theImage.CGImage];
    
    // setting up Gaussian Blur (we could use one of many filters offered by Core Image)
    CIFilter *filter = [CIFilter filterWithName:@"CIGaussianBlur"];
    [filter setValue:inputImage forKey:kCIInputImageKey];
    [filter setValue:[NSNumber numberWithFloat:10.0f] forKey:@"inputRadius"];
    CIImage *result = [filter valueForKey:kCIOutputImageKey];
    // CIGaussianBlur has a tendency to shrink the image a little,
    // this ensures it matches up exactly to the bounds of our original image
    CGImageRef cgImage = [context createCGImage:result fromRect:[inputImage extent]];
    //return [UIImage imageWithCGImage:cgImage];
    return [UIImage imageWithCGImage:cgImage scale:8.0 orientation:UIImageOrientationUp];
}

@end
